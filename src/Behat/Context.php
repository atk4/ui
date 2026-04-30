<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Atk4\Core\WarnDynamicPropertyTrait;
use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Behat\Hook\Scope\StepScope;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\WebAssert;
use Behat\MinkExtension\Context\RawMinkContext;

class Context extends RawMinkContext implements BehatContext
{
    use JsCoverageContextTrait;
    use RwDemosContextTrait;
    use WarnDynamicPropertyTrait;

    #[\Override]
    public function getSession($name = null): MinkSession
    {
        return new MinkSession($this->getMink()->getSession($name));
    }

    #[\Override]
    public function assertSession($name = null): WebAssert
    {
        return new class($this->getSession($name)) extends WebAssert {
            #[\Override]
            protected function cleanUrl($url)
            {
                // fix https://github.com/minkphp/Mink/issues/656
                return $url;
            }
        };
    }

    protected function getScenario(StepScope $event): ScenarioInterface
    {
        foreach ($event->getFeature()->getScenarios() as $scenario) {
            $scenarioSteps = $scenario->getSteps();
            if (count($scenarioSteps) > 0
                && array_first($scenarioSteps)->getLine() <= $event->getStep()->getLine()
                && array_last($scenarioSteps)->getLine() >= $event->getStep()->getLine()
            ) {
                return $scenario;
            }
        }

        throw new \Exception('Unable to find scenario');
    }

    /**
     * @BeforeStep
     */
    public function closeAllToasts(BeforeStepScope $event): void
    {
        if (!$this->getSession()->isStarted()) {
            return;
        }

        if (!str_starts_with($event->getStep()->getText(), 'Toast display should contain text ')
            && $event->getStep()->getText() !== 'No toast should be displayed'
        ) {
            $this->getSession()->executeScript('jQuery(\'.toast-box > .ui.toast\').toast(\'destroy\')');
        }
    }

    /**
     * @AfterStep
     */
    public function waitUntilLoadingAndAnimationFinished(AfterStepScope $event): void
    {
        if (!$this->getSession()->isStarted()) {
            return;
        }

        $this->jqueryWait();
        $this->disableAnimations();

        if (!str_contains($this->getScenario($event)->getTitle() ?? '', 'exception is displayed')) {
            $this->assertNoException();
        }
        $this->assertNoDuplicateId();

        $this->saveJsCoverage();
    }

    protected function getFinishedScript(): string
    {
        return 'document.readyState === \'complete\' && typeof jQuery !== \'undefined\' && typeof atk !== \'undefined\''
            . ' && jQuery.active === 0' // no jQuery AJAX request, https://github.com/jquery/jquery/blob/3.6.4/src/ajax.js#L582
            . ' && jQuery.timers.length === 0' // no jQuery animation, https://github.com/jquery/jquery/blob/3.6.4/src/effects/animatedSelector.js#L10
            . ' && document.querySelectorAll(\'.ui.animating:not(.looping)\').length === 0' // no Fomantic-UI animation, https://github.com/fomantic/Fomantic-UI/blob/2.9.2/src/definitions/modules/dimmer.js#L358
            . ' && atk.vueService.areComponentsLoaded()';
    }

    /**
     * Wait till jQuery AJAX request finished and no animation is perform.
     *
     * @param list<mixed> $args
     */
    protected function jqueryWait(string $extraWaitCondition = 'true', array $args = [], int $maxWaitdurationMs = 5000): void
    {
        $finishedScript = '(' . $this->getFinishedScript() . ') && (' . $extraWaitCondition . ')';

        $s = microtime(true);
        $c = 0;
        while (microtime(true) - $s <= $maxWaitdurationMs / 1000) {
            $this->getSession()->wait($maxWaitdurationMs, $finishedScript, $args);
            usleep(10_000);
            if ($this->getSession()->evaluateScript($finishedScript, $args)) { // TODO wait() uses evaluateScript(), dedup
                if (++$c >= 2) {
                    return;
                }
            } else {
                $c = 0;
                usleep(20_000);
            }
        }

        throw new \Exception('jQuery did not finish within a time limit');
    }

    protected function disableAnimations(): void
    {
        // disable all CSS/jQuery animations/transitions
        $toCssFx = static function (string $selector, array $cssPairs): string {
            $css = [];
            foreach ($cssPairs as $k => $v) {
                foreach ([$k, '-moz-' . $k, '-webkit-' . $k] as $k2) {
                    $css[] = $k2 . ': ' . $v . ' !important;';
                }
            }

            return $selector . ' { ' . implode(' ', $css) . ' }';
        };

        $durationAnimation = 0.005;
        $durationToast = 5;
        $css = $toCssFx('*', [
            'animation-delay' => $durationAnimation . 's',
            'animation-duration' => $durationAnimation . 's',
            'transition-delay' => $durationAnimation . 's',
            'transition-duration' => $durationAnimation . 's',
        ]) . $toCssFx('.ui.toast-container .toast-box .progressing.wait', [
            'animation-duration' => $durationToast . 's',
            'transition-duration' => $durationToast . 's',
        ]);

        $this->getSession()->executeScript(
            'if (Array.prototype.filter.call(document.getElementsByTagName(\'style\'), (e) => e.getAttribute(\'about\') === \'atk4-ui-behat\').length === 0) {'
            . ' $(\'<style about="atk4-ui-behat">' . $css . '</style>\').appendTo(\'head\');'
            . ' jQuery.fx.off = true;'
            // fix self::getFinishedScript() detection for Firefox - document.readyState is updated after at least part of a new page has been loaded
            . ' window.addEventListener(\'beforeunload\', (event) => jQuery.active++);'
            . ' }'
        );
    }

    protected function assertNoException(): void
    {
        foreach ($this->getSession()->getPage()->findAll('css', 'div.ui.negative.icon.message > div.content > div.header') as $elem) {
            if ($elem->getText() === 'Critical Error') {
                echo "\n" . trim(preg_replace(
                    '~(?<=\n)(\d+|Stack Trace\n#FileObjectMethod)(?=\n)~',
                    '',
                    preg_replace(
                        '~(^.*?)?\s*Critical Error\s*\n\s*|(\s*\n)+\s{0,16}~s',
                        "\n",
                        strip_tags($elem->find('xpath', '../../..')->getHtml())
                    )
                )) . "\n";

                throw new \Exception('Page contains uncaught exception');
            }
        }
    }

    protected function assertNoDuplicateId(): void
    {
        [$invalidIds, $duplicateIds] = $this->getSession()->evaluateScript(<<<'EOF'
            (function () {
                const idRegex = /^[a-z_][0-9a-z_\-]*$/is;
                const invalidIds = [];
                const duplicateIds = [];
                [...(new Set(
                    $('[id]').map(function () {
                        return this.id;
                    })
                ))].forEach(function (id) {
                    if (!id.match(idRegex)) {
                        invalidIds.push(id);
                    } else {
                        const elems = $('[id="' + id + '"]');
                        if (elems.length > 1) {
                            duplicateIds.push(id);
                        }
                    }
                });

                return [invalidIds, duplicateIds];
            })();
            EOF);

        if (count($invalidIds) > 0) {
            throw new \Exception('Page contains element with invalid ID: ' . implode(', ', array_map(static fn ($v) => '"' . $v . '"', $invalidIds)));
        }

        if (count($duplicateIds) > 0) {
            throw new \Exception('Page contains elements with duplicate ID: ' . implode(', ', array_map(static fn ($v) => '"' . $v . '"', $duplicateIds)));
        }
    }

    private function quoteXpath(string $value): string
    {
        return str_contains($value, '\'')
            ? 'concat(\'' . str_replace('\'', '\', "\'", \'', $value) . '\')'
            : '\'' . $value . '\'';
    }

    /**
     * @return array{ 'css'|'xpath', string }
     */
    protected function parseSelector(string $selector): array
    {
        if (preg_match('~^\(*//~s', $selector)) {
            // add support for standard CSS class selector
            $xpath = preg_replace_callback(
                '~\'(?:[^\']+|\'\')*+\'\K|"(?:[^"]+|"")*+"\K|(?<=\w|\*)\.([\w\-]+)~s',
                static function ($matches) {
                    if ($matches[0] === '') {
                        return '';
                    }

                    return '[contains(concat(\' \', normalize-space(@class), \' \'), \' ' . $matches[1] . ' \')]';
                },
                $selector
            );

            // add NBSP support for normalize-space() xpath function
            $xpath = preg_replace(
                '~(?<![\w\-])normalize-space\([^()\'"]*\)~',
                'normalize-space(translate($0, \'' . "\u{00a0}" . '\', \' \'))',
                $xpath
            );

            return ['xpath', $xpath];
        }

        return ['css', $selector];
    }

    /**
     * @return list<NodeElement>
     */
    protected function findElements(?NodeElement $context, string $selector): array
    {
        $selectorParsed = $this->parseSelector($selector);
        $elements = ($context ?? $this->getSession()->getPage())->findAll($selectorParsed[0], $selectorParsed[1]);

        if (count($elements) === 0) {
            throw new \Exception('No element found in ' . ($context === null ? 'page' : 'element')
                . ' using selector: ' . $selector);
        }

        return $elements;
    }

    protected function findElement(?NodeElement $context, string $selector): NodeElement
    {
        $elements = $this->findElements($context, $selector);

        return $elements[0];
    }

    protected function unquoteStepArgument(string $value): string
    {
        assert(str_starts_with($value, '"') && str_ends_with($value, '"'));
        $res = substr($value, 1, -1);

        // inspired by https://github.com/Behat/MinkExtension/blob/v2.2/src/Behat/MinkExtension/Context/MinkContext.php#L567
        $res = str_replace(['\\\\', '\"', '\n', '\u{00a0}'], ['x\\\x', '"', "\n", "\u{00a0}" /* Unicode NBSP */], $res);

        return str_replace('x\\\x', '\\', $res);
    }

    /**
     * Sleep for a certain time in ms.
     *
     * @When ~^I wait ("(?:\\[\\"]|[^"])*+") ms$~
     */
    public function iWait(string $ms): void
    {
        $ms = (int) $this->unquoteStepArgument($ms);

        $this->getSession()->wait($ms);
    }

    /**
     * @When ~^I write ("(?:\\[\\"]|[^"])*+") into selector ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iPressWrite(string $text, string $selector): void
    {
        $text = $this->unquoteStepArgument($text);
        $selector = $this->unquoteStepArgument($selector);

        if ($selector === 'document' && $text === '[escape]') {
            $this->getSession()->executeScript('document.dispatchEvent(new KeyboardEvent(\'keydown\', {keyCode: 27, which: 27}))');

            return;
        }

        $elem = $this->findElement(null, $selector);
        $this->getSession()->keyboardWrite($elem, $text);
    }

    /**
     * @When ~^I drag selector ("(?:\\[\\"]|[^"])*+") onto selector ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iDragElementOnto(string $selector, string $selectorTarget): void
    {
        $selector = $this->unquoteStepArgument($selector);
        $selectorTarget = $this->unquoteStepArgument($selectorTarget);

        $elem = $this->findElement(null, $selector);
        $elemTarget = $this->findElement(null, $selectorTarget);
        $this->getSession()->getDriver()->dragTo($elem->getXpath(), $elemTarget->getXpath());
    }

    // {{{ button

    /**
     * @When ~^I press button ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iPressButton(string $buttonLabel): void
    {
        $buttonLabel = $this->unquoteStepArgument($buttonLabel);

        $button = $this->findElement(null, '//div[text()=' . $this->quoteXpath($buttonLabel) . ']');
        $button->click();
    }

    /**
     * @Then ~^I see button ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iSeeButton(string $buttonLabel): void
    {
        $buttonLabel = $this->unquoteStepArgument($buttonLabel);

        $this->findElement(null, '//div[text()=' . $this->quoteXpath($buttonLabel) . ']');
    }

    /**
     * @Then ~^I don't see button ("(?:\\[\\"]|[^"])*+")$~
     */
    public function idontSeeButton(string $text): void
    {
        $text = $this->unquoteStepArgument($text);

        $element = $this->findElement(null, '//div[text()=' . $this->quoteXpath($text) . ']');
        if (!str_contains($element->getAttribute('style'), 'display: none')) {
            throw new \Exception('Element with text "' . $text . '" must be invisible');
        }
    }

    // }}}

    // {{{ link

    /**
     * @Given ~^I click link ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iClickLink(string $label): void
    {
        $label = $this->unquoteStepArgument($label);

        $this->findElement(null, '//a[text()=' . $this->quoteXpath($label) . ']')->click();
    }

    /**
     * @When ~^I click using selector ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iClickUsingSelector(string $selector): void
    {
        $selector = $this->unquoteStepArgument($selector);

        $element = $this->findElement(null, $selector);
        $element->click();
    }

    /**
     * \Behat\Mink\Driver\Selenium2Driver::clickOnElement() does not wait until AJAX is completed after scroll.
     *
     * One solution can be waiting for AJAX after each \WebDriver\AbstractWebDriver::curl() call.
     *
     * @When ~^PATCH DRIVER I click using selector ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iClickPatchedUsingSelector(string $selector): void
    {
        $selector = $this->unquoteStepArgument($selector);

        $element = $this->findElement(null, $selector);

        $driver = $this->getSession()->getDriver();
        \Closure::bind(static function () use ($driver, $element) {
            $driver->mouseOverElement($driver->findElement($element->getXpath()));
        }, null, MinkSeleniumDriver::class)();
        $this->jqueryWait();

        $element->click();
    }

    /**
     * @When ~^I click paginator page ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iClickPaginatorPage(string $pageNumber): void
    {
        $pageNumber = $this->unquoteStepArgument($pageNumber);

        $element = $this->findElement(null, 'a.item[data-page="' . $pageNumber . '"]');
        $element->click();
    }

    /**
     * @When ~^I fill field using ("(?:\\[\\"]|[^"])*+") with ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iFillField(string $selector, string $value): void
    {
        $selector = $this->unquoteStepArgument($selector);
        $value = $this->unquoteStepArgument($value);

        $element = $this->findElement(null, $selector);
        $element->setValue($value);
    }

    // }}}

    // {{{ modal

    /**
     * @When ~^I press Modal button ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iPressModalButton(string $buttonLabel): void
    {
        $buttonLabel = $this->unquoteStepArgument($buttonLabel);

        $modal = $this->findElement(null, '.modal.visible.active.front');
        $button = $this->findElement($modal, '//div[text()=' . $this->quoteXpath($buttonLabel) . ']');
        $button->click();
    }

    /**
     * @Then ~^Modal is open with text ("(?:\\[\\"]|[^"])*+")$~
     * @Then ~^Modal is open with text ("(?:\\[\\"]|[^"])*+") in selector ("(?:\\[\\"]|[^"])*+")$~
     *
     * Check if text is present in modal or dynamic modal.
     */
    public function modalIsOpenWithText(string $text, string $selector = '"*"'): void
    {
        $text = $this->unquoteStepArgument($text);
        $selector = $this->unquoteStepArgument($selector);

        $modal = $this->findElement(null, '.modal.visible.active.front');
        $this->findElement($modal, '//' . $selector . '[text()[normalize-space()=' . $this->quoteXpath($text) . ']]');
    }

    /**
     * @When ~^I fill Modal field ("(?:\\[\\"]|[^"])*+") with ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iFillModalField(string $fieldName, string $value): void
    {
        $fieldName = $this->unquoteStepArgument($fieldName);
        $value = $this->unquoteStepArgument($value);

        $modal = $this->findElement(null, '.modal.visible.active.front');
        $field = $modal->find('named', ['field', $fieldName]);
        $field->setValue($value);
    }

    /**
     * @When ~^I click close modal$~
     */
    public function iClickCloseModal(): void
    {
        $modal = $this->findElement(null, '.modal.visible.active.front');
        $closeIcon = $this->findElement($modal, '//i.icon.close');
        $closeIcon->click();
    }

    /**
     * @When ~^I hide js modal$~
     */
    public function iHideJsModal(): void
    {
        $modal = $this->findElement(null, '.modal.visible.active.front');
        $this->getSession()->executeScript('$(arguments[0]).modal(\'hide\')', [$modal]);
    }

    // }}}

    // {{{ panel

    /**
     * @Then ~^Panel is open$~
     */
    public function panelIsOpen(): void
    {
        $this->findElement(null, '.atk-right-panel.atk-visible');
    }

    /**
     * @Then ~^Panel is open with text ("(?:\\[\\"]|[^"])*+")$~
     * @Then ~^Panel is open with text ("(?:\\[\\"]|[^"])*+") in selector ("(?:\\[\\"]|[^"])*+")$~
     */
    public function panelIsOpenWithText(string $text, string $selector = '"*"'): void
    {
        $text = $this->unquoteStepArgument($text);
        $selector = $this->unquoteStepArgument($selector);

        $panel = $this->findElement(null, '.atk-right-panel.atk-visible');
        $this->findElement($panel, '//' . $selector . '[text()[normalize-space()=' . $this->quoteXpath($text) . ']]');
    }

    /**
     * @When ~^I fill Panel field ("(?:\\[\\"]|[^"])*+") with ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iFillPanelField(string $fieldName, string $value): void
    {
        $fieldName = $this->unquoteStepArgument($fieldName);
        $value = $this->unquoteStepArgument($value);

        $panel = $this->findElement(null, '.atk-right-panel.atk-visible');
        $field = $panel->find('named', ['field', $fieldName]);
        $field->setValue($value);
    }

    /**
     * @When ~^I press Panel button ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iPressPanelButton(string $buttonLabel): void
    {
        $buttonLabel = $this->unquoteStepArgument($buttonLabel);

        $panel = $this->findElement(null, '.atk-right-panel.atk-visible');
        $button = $this->findElement($panel, '//div[text()=' . $this->quoteXpath($buttonLabel) . ']');
        $button->click();
    }

    // }}}

    // {{{ tab

    /**
     * @When ~^I click tab with title ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iClickTabWithTitle(string $tabTitle): void
    {
        $tabTitle = $this->unquoteStepArgument($tabTitle);

        $tabMenu = $this->findElement(null, '.ui.tabbed.menu');
        $link = $this->findElement($tabMenu, '//div[text()=' . $this->quoteXpath($tabTitle) . ']');
        $link->click();
    }

    /**
     * @Then ~^Active tab should be ("(?:\\[\\"]|[^"])*+")$~
     */
    public function activeTabShouldBe(string $title): void
    {
        $title = $this->unquoteStepArgument($title);

        $tab = $this->findElement(null, '.ui.tabbed.menu > .item.active');
        if ($tab->getText() !== $title) {
            throw new \Exception('Active tab is not ' . $title);
        }
    }

    // }}}

    // {{{ input

    /**
     * @Then ~^input ("(?:\\[\\"]|[^"])*+") value should start with ("(?:\\[\\"]|[^"])*+")$~
     */
    public function inputValueShouldStartWith(string $inputName, string $text): void
    {
        $inputName = $this->unquoteStepArgument($inputName);
        $text = $this->unquoteStepArgument($text);

        $field = $this->assertSession()->fieldExists($inputName);

        if (!str_starts_with($field->getValue(), $text)) {
            throw new \Exception('Field value ' . $field->getValue() . ' does not start with ' . $text);
        }
    }

    /**
     * @When ~^I search grid for ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iSearchGridFor(string $text): void
    {
        $text = $this->unquoteStepArgument($text);

        $search = $this->findElement(null, 'input.atk-grid-search');
        $search->setValue($text);
    }

    /**
     * @When ~^I select ("(?:\\[\\"]|[^"])*+") in lookup ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iSelectInLookup(string $value, string $inputName): void
    {
        $value = $this->unquoteStepArgument($value);
        $inputName = $this->unquoteStepArgument($inputName);

        // get dropdown item from Fomantic-UI which is direct parent of input HTML element
        $isSelectorXpath = $this->parseSelector($inputName)[0] === 'xpath';
        $lookupElem = $this->findElement(null, ($isSelectorXpath ? $inputName : '//input[@name=' . $this->quoteXpath($inputName) . ']') . '/parent::div');

        if ($value === '') {
            $this->findElement($lookupElem, 'i.remove.icon')->click();

            return;
        }

        // open dropdown and wait till fully opened
        $this->findElement($lookupElem, 'i.dropdown.icon')->click();
        $this->jqueryWait('$(arguments[0]).hasClass(\'visible\')', [$lookupElem]);

        // select value
        $valueElem = $this->findElement($lookupElem, '//div.menu//div.item[text()=' . $this->quoteXpath($value) . ']');
        if ($valueElem->getAttribute('data-value') === null) { // Multiline vue dropdown
            $valueElem->click();
        } else {
            $this->getSession()->executeScript('$(arguments[0]).dropdown(\'set selected\', arguments[1]);', [$lookupElem, $valueElem->getAttribute('data-value')]);
        }
        $this->jqueryWait();

        // hide dropdown and wait till fully closed
        $this->getSession()->executeScript('$(arguments[0]).dropdown(\'hide\');', [$lookupElem]);
        $this->jqueryWait('!$(arguments[0]).hasClass(\'visible\')', [$lookupElem]);
    }

    /**
     * @When ~^I select file input ("(?:\\[\\"]|[^"])*+") with ("(?:\\[\\"]|[^"])*+") as ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iSelectFile(string $inputName, string $fileContent, string $fileName): void
    {
        $inputName = $this->unquoteStepArgument($inputName);
        $fileContent = $this->unquoteStepArgument($fileContent);
        $fileName = $this->unquoteStepArgument($fileName);

        $element = $this->findElement(null, '//input[@name=' . $this->quoteXpath($inputName) . ' and @type="hidden"]/following-sibling::input[@type="file"]');
        $this->getSession()->executeScript(<<<'EOF'
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(new File([new Uint8Array(arguments[1])], arguments[2]));
            arguments[0].files = dataTransfer.files;
            $(arguments[0]).trigger('change');
            EOF, [$element, array_map('ord', str_split($fileContent)), $fileName]);
    }

    private function getScopeBuilderRuleElem(string $ruleName): NodeElement
    {
        return $this->findElement(null, '.vqb-rule[data-name=' . $ruleName . ']');
    }

    /**
     * Generic ScopeBuilder rule with select operator and input value.
     *
     * @Then ~^rule ("(?:\\[\\"]|[^"])*+") operator is ("(?:\\[\\"]|[^"])*+") and value is ("(?:\\[\\"]|[^"])*+")$~
     */
    public function scopeBuilderRule(string $name, string $operator, string $value): void
    {
        $name = $this->unquoteStepArgument($name);
        $operator = $this->unquoteStepArgument($operator);
        $value = $this->unquoteStepArgument($value);

        $rule = $this->getScopeBuilderRuleElem($name);
        $this->assertSelectedValue($rule, $operator, '.vqb-rule-operator select');
        $this->assertInputValue($rule, $value);
    }

    /**
     * HasOne reference or enum type rule for ScopeBuilder.
     *
     * @Then ~^reference rule ("(?:\\[\\"]|[^"])*+") operator is ("(?:\\[\\"]|[^"])*+") and value is ("(?:\\[\\"]|[^"])*+")$~
     */
    public function scopeBuilderReferenceRule(string $name, string $operator, string $value): void
    {
        $name = $this->unquoteStepArgument($name);
        $operator = $this->unquoteStepArgument($operator);
        $value = $this->unquoteStepArgument($value);

        $rule = $this->getScopeBuilderRuleElem($name);
        $this->assertSelectedValue($rule, $operator, '.vqb-rule-operator select');
        $this->assertDropdownValue($rule, $value, '.vqb-rule-input .active.item');
    }

    /**
     * HasOne select or enum type rule for ScopeBuilder.
     *
     * @Then ~^select rule ("(?:\\[\\"]|[^"])*+") operator is ("(?:\\[\\"]|[^"])*+") and value is ("(?:\\[\\"]|[^"])*+")$~
     */
    public function scopeBuilderSelectRule(string $name, string $operator, string $value): void
    {
        $name = $this->unquoteStepArgument($name);
        $operator = $this->unquoteStepArgument($operator);
        $value = $this->unquoteStepArgument($value);

        $rule = $this->getScopeBuilderRuleElem($name);
        $this->assertSelectedValue($rule, $operator, '.vqb-rule-operator select');
        $this->assertSelectedValue($rule, $value, '.vqb-rule-input select');
    }

    /**
     * Date, Time or Datetime rule for ScopeBuilder.
     *
     * @Then ~^date rule ("(?:\\[\\"]|[^"])*+") operator is ("(?:\\[\\"]|[^"])*+") and value is ("(?:\\[\\"]|[^"])*+")$~
     */
    public function scopeBuilderDateRule(string $name, string $operator, string $value): void
    {
        $name = $this->unquoteStepArgument($name);
        $operator = $this->unquoteStepArgument($operator);
        $value = $this->unquoteStepArgument($value);

        $rule = $this->getScopeBuilderRuleElem($name);
        $this->assertSelectedValue($rule, $operator, '.vqb-rule-operator select');
        $this->assertInputValue($rule, $value);
    }

    /**
     * Boolean type rule for ScopeBuilder.
     *
     * @Then ~^bool rule ("(?:\\[\\"]|[^"])*+") has value ("(?:\\[\\"]|[^"])*+")$~
     */
    public function scopeBuilderBoolRule(string $name, string $value): void
    {
        $name = $this->unquoteStepArgument($name);
        $value = $this->unquoteStepArgument($value);

        $this->getScopeBuilderRuleElem($name);
        $idx = ($value === 'Yes') ? 0 : 1;
        $isChecked = $this->getSession()->evaluateScript('$(\'[data-name="' . $name . '"]\').find(\'input\')[' . $idx . '].checked');
        if (!$isChecked) {
            throw new \Exception('Radio value selected is not: ' . $value);
        }
    }

    /**
     * @Then ~^I check if input value for ("(?:\\[\\"]|[^"])*+") match text in ("(?:\\[\\"]|[^"])*+")$~
     */
    public function compareInputValueText(string $compareSelector, string $compareToSelector): void
    {
        $compareSelector = $this->unquoteStepArgument($compareSelector);
        $compareToSelector = $this->unquoteStepArgument($compareToSelector);

        if ($this->findElement(null, $compareSelector)->getValue() !== $this->findElement(null, $compareToSelector)->getText()) {
            throw new \Exception('Input value does not match between: ' . $compareSelector . ' and ' . $compareToSelector);
        }
    }

    /**
     * @Then ~^I check if input value for ("(?:\\[\\"]|[^"])*+") match text ("(?:\\[\\"]|[^"])*+")$~
     */
    public function compareInputValueToText(string $selector, string $text): void
    {
        $selector = $this->unquoteStepArgument($selector);
        $text = $this->unquoteStepArgument($text);

        $inputValue = $this->findElement(null, $selector)->getValue();
        if ($inputValue !== $text) {
            throw new \Exception('Input value does not match: ' . $inputValue . ', expected: ' . $text);
        }
    }

    // }}}

    // {{{ misc

    /**
     * @Then ~^dump ("(?:\\[\\"]|[^"])*+")$~
     */
    public function dump(string $arg1): void
    {
        $arg1 = $this->unquoteStepArgument($arg1);

        $element = $this->getSession()->getPage()->find('xpath', '//div[text()=' . $this->quoteXpath($arg1) . ']');
        var_dump($element->getOuterHtml());
    }

    /**
     * @When ~^I click filter column name ("(?:\\[\\"]|[^"])*+")$~
     */
    public function iClickFilterColumnName(string $columnName): void
    {
        $columnName = $this->unquoteStepArgument($columnName);

        $column = $this->findElement(null, "th[data-column='" . $columnName . "']");
        $icon = $this->findElement($column, 'i');
        $icon->click();
    }

    /**
     * @Then ~^container ("(?:\\[\\"]|[^"])*+") should display ("(?:\\[\\"]|[^"])*+") item\(s\)$~
     */
    public function containerShouldHaveNumberOfItem(string $selector, string $numberOfitems): void
    {
        $selector = $this->unquoteStepArgument($selector);
        $numberOfitems = (int) $this->unquoteStepArgument($numberOfitems);

        $items = $this->getSession()->getPage()->findAll('css', $selector);
        $count = 0;
        foreach ($items as $el => $item) {
            ++$count;
        }
        if ($count !== $numberOfitems) {
            throw new \Exception('Items does not match. There were ' . $count . ' item in container');
        }
    }

    /**
     * @When ~^I scroll to top$~
     */
    public function iScrollToTop(): void
    {
        $this->getSession()->executeScript('window.scrollTo(0, 0)');
    }

    /**
     * @When ~^I scroll to bottom$~
     */
    public function iScrollToBottom(): void
    {
        $this->getSession()->executeScript('window.scrollTo(0, 100 * 1000)');
    }

    /**
     * @Then ~^Toast display should contain text ("(?:\\[\\"]|[^"])*+")$~
     */
    public function toastDisplayShouldContainText(string $text): void
    {
        $text = $this->unquoteStepArgument($text);

        $toastContainer = $this->findElement(null, '.ui.toast-container');
        $toastText = $this->findElement($toastContainer, '.content')->getText();
        if (!str_contains($toastText, $text)) {
            throw new \Exception('Toast text "' . $toastText . '" does not contain "' . $text . '"');
        }
    }

    /**
     * @Then ~^No toast should be displayed$~
     */
    public function noToastShouldBeDisplayed(): void
    {
        $toasts = $this->getSession()->getPage()->findAll('css', '.ui.toast-container .toast-box');
        if (count($toasts) > 0) {
            throw new \Exception('Toast is displayed: "' . $this->findElement(array_first($toasts), '.content')->getText() . '"');
        }
    }

    /**
     * Remove once https://github.com/Behat/MinkExtension/pull/386 and
     * https://github.com/minkphp/Mink/issues/656 are fixed and released.
     *
     * @Then ~^PATCH MINK the URL should match ("(?:\\[\\"]|[^"])*+")$~
     */
    public function assertUrlRegExp(string $pattern): void
    {
        $pattern = $this->unquoteStepArgument($pattern);

        $this->assertSession()->addressMatches($pattern);
    }

    /**
     * @Then ~^I check if text in ("(?:\\[\\"]|[^"])*+") match text in ("(?:\\[\\"]|[^"])*+")$~
     */
    public function compareElementText(string $compareSelector, string $compareToSelector): void
    {
        $compareSelector = $this->unquoteStepArgument($compareSelector);
        $compareToSelector = $this->unquoteStepArgument($compareToSelector);

        if ($this->findElement(null, $compareSelector)->getText() !== $this->findElement(null, $compareToSelector)->getText()) {
            throw new \Exception('Text does not match between: ' . $compareSelector . ' and ' . $compareToSelector);
        }
    }

    /**
     * @Then ~^I check if text in ("(?:\\[\\"]|[^"])*+") match text ("(?:\\[\\"]|[^"])*+")$~
     */
    public function textInContainerShouldMatch(string $selector, string $text): void
    {
        $selector = $this->unquoteStepArgument($selector);
        $text = $this->unquoteStepArgument($text);

        if ($this->findElement(null, $selector)->getText() !== $text) {
            throw new \Exception('Container with selector: ' . $selector . ' does not match text: ' . $text);
        }
    }

    /**
     * @Then ~^I check if text in ("(?:\\[\\"]|[^"])*+") match regex ("(?:\\[\\"]|[^"])*+")$~
     */
    public function textInContainerShouldMatchRegex(string $selector, string $regex): void
    {
        $selector = $this->unquoteStepArgument($selector);
        $regex = $this->unquoteStepArgument($regex);

        if (!preg_match($regex, $this->findElement(null, $selector)->getText())) {
            throw new \Exception('Container with selector: ' . $selector . ' does not match regex: ' . $regex);
        }
    }

    /**
     * @Then ~^Element ("(?:\\[\\"]|[^"])*+") attribute ("(?:\\[\\"]|[^"])*+") should contain text ("(?:\\[\\"]|[^"])*+")$~
     */
    public function elementAttributeShouldContainText(string $selector, string $attribute, string $text): void
    {
        $selector = $this->unquoteStepArgument($selector);
        $attribute = $this->unquoteStepArgument($attribute);
        $text = $this->unquoteStepArgument($text);

        $element = $this->findElement(null, $selector);
        $attr = $element->getAttribute($attribute);
        if (!str_contains($attr, $text)) {
            throw new \Exception('Element "' . $selector . '" attribute "' . $attribute . '" does not contain "' . $text . '"');
        }
    }

    /**
     * @Then ~^Element ("(?:\\[\\"]|[^"])*+") should contain class ("(?:\\[\\"]|[^"])*+")$~
     */
    public function elementShouldContainClass(string $selector, string $class): void
    {
        $selector = $this->unquoteStepArgument($selector);
        $class = $this->unquoteStepArgument($class);

        $element = $this->findElement(null, $selector);
        $classes = explode(' ', $element->getAttribute('class'));
        if (!in_array($class, $classes, true)) {
            throw new \Exception('Element "' . $selector . '" does not contain "' . $class . '" class');
        }
    }

    /**
     * @Then ~^Element ("(?:\\[\\"]|[^"])*+") should not contain class ("(?:\\[\\"]|[^"])*+")$~
     */
    public function elementShouldNotContainClass(string $selector, string $class): void
    {
        $selector = $this->unquoteStepArgument($selector);
        $class = $this->unquoteStepArgument($class);

        $element = $this->findElement(null, $selector);
        $classes = explode(' ', $element->getAttribute('class'));
        if (in_array($class, $classes, true)) {
            throw new \Exception('Element "' . $selector . '" contains "' . $class . '" class');
        }
    }

    // }}}

    /**
     * Find a dropdown component within an HTML element
     * and check if value is set in dropdown.
     */
    private function assertDropdownValue(NodeElement $element, string $value, string $selector): void
    {
        if ($this->findElement($element, $selector)->getText() !== $value) {
            throw new \Exception('Value: "' . $value . '" not set using selector: ' . $selector);
        }
    }

    /**
     * Find a select input type within an HTML element
     * and check if value is selected.
     */
    private function assertSelectedValue(NodeElement $element, string $value, string $selector): void
    {
        if ($this->findElement($element, $selector)->getValue() !== $value) {
            throw new \Exception('Value: "' . $value . '" not set using selector: ' . $selector);
        }
    }

    /**
     * Find an input within an HTML element and check
     * if value is set.
     */
    private function assertInputValue(NodeElement $element, string $value, string $selector = 'input'): void
    {
        if ($this->findElement($element, $selector)->getValue() !== $value) {
            throw new \Exception('Input value not is not: ' . $value);
        }
    }
}
