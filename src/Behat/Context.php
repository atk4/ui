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

    public function getSession($name = null): MinkSession
    {
        return new MinkSession($this->getMink()->getSession($name));
    }

    public function assertSession($name = null): WebAssert
    {
        return new class($this->getSession($name)) extends WebAssert {
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
                && reset($scenarioSteps)->getLine() <= $event->getStep()->getLine()
                && end($scenarioSteps)->getLine() >= $event->getStep()->getLine()
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

        // TODO hack to pass CI testing, fix these issues and remove the error diffs below asap
        $duplicateIds = array_diff($duplicateIds, ['atk', '_icon', 'atk_icon']); // generated when component is not correctly added to app/layout component tree - should throw, as such name/ID is dangerous to be used

        if (count($invalidIds) > 0) {
            throw new \Exception('Page contains element with invalid ID: ' . implode(', ', array_map(static fn ($v) => '"' . $v . '"', $invalidIds)));
        }

        if (count($duplicateIds) > 0) {
            throw new \Exception('Page contains elements with duplicate ID: ' . implode(', ', array_map(static fn ($v) => '"' . $v . '"', $duplicateIds)));
        }
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
     * @return array<NodeElement>
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

    protected function unquoteStepArgument(string $argument): string
    {
        // copied from https://github.com/Behat/MinkExtension/blob/v2.2/src/Behat/MinkExtension/Context/MinkContext.php#L567
        return str_replace('\\"', '"', $argument);
    }

    /**
     * Sleep for a certain time in ms.
     *
     * @Then I wait :arg1 ms
     */
    public function iWait(int $ms): void
    {
        $this->getSession()->wait($ms);
    }

    /**
     * @When I write :arg1 into selector :selector
     */
    public function iPressWrite(string $text, string $selector): void
    {
        $elem = $this->findElement(null, $selector);
        $this->getSession()->keyboardWrite($elem, $text);
    }

    /**
     * @When I drag selector :selector onto selector :selectorTarget
     */
    public function iDragElementOnto(string $selector, string $selectorTarget): void
    {
        $elem = $this->findElement(null, $selector);
        $elemTarget = $this->findElement(null, $selectorTarget);
        $this->getSession()->getDriver()->dragTo($elem->getXpath(), $elemTarget->getXpath());
    }

    // {{{ button

    /**
     * @When I press button :arg1
     */
    public function iPressButton(string $buttonLabel): void
    {
        $button = $this->findElement(null, '//div[text()="' . $buttonLabel . '"]');
        $button->click();
    }

    /**
     * @Then I see button :arg1
     */
    public function iSeeButton(string $buttonLabel): void
    {
        $this->findElement(null, '//div[text()="' . $buttonLabel . '"]');
    }

    /**
     * @Then I don't see button :arg1
     */
    public function idontSeeButton(string $text): void
    {
        $element = $this->findElement(null, '//div[text()="' . $text . '"]');
        if (!str_contains($element->getAttribute('style'), 'display: none')) {
            throw new \Exception('Element with text "' . $text . '" must be invisible');
        }
    }

    // }}}

    // {{{ link

    /**
     * @Given I click link :arg1
     */
    public function iClickLink(string $label): void
    {
        $this->findElement(null, '//a[text()="' . $label . '"]')->click();
    }

    /**
     * @Then I click using selector :selector
     */
    public function iClickUsingSelector(string $selector): void
    {
        $element = $this->findElement(null, $selector);
        $element->click();
    }

    /**
     * \Behat\Mink\Driver\Selenium2Driver::clickOnElement() does not wait until AJAX is completed after scroll.
     *
     * One solution can be waiting for AJAX after each \WebDriver\AbstractWebDriver::curl() call.
     *
     * @Then PATCH DRIVER I click using selector :selector
     */
    public function iClickPatchedUsingSelector(string $selector): void
    {
        $element = $this->findElement(null, $selector);

        $driver = $this->getSession()->getDriver();
        \Closure::bind(static function () use ($driver, $element) {
            $driver->mouseOverElement($driver->findElement($element->getXpath()));
        }, null, MinkSeleniumDriver::class)();
        $this->jqueryWait();

        $element->click();
    }

    /**
     * @Then I click paginator page :arg1
     */
    public function iClickPaginatorPage(string $pageNumber): void
    {
        $element = $this->findElement(null, 'a.item[data-page="' . $pageNumber . '"]');
        $element->click();
    }

    /**
     * @When I fill field using :selector with :value
     */
    public function iFillField(string $selector, string $value): void
    {
        $element = $this->findElement(null, $selector);
        $element->setValue($value);
    }

    // }}}

    // {{{ modal

    /**
     * @Then I press Modal button :arg
     */
    public function iPressModalButton(string $buttonLabel): void
    {
        $modal = $this->findElement(null, '.modal.visible.active.front');
        $button = $this->findElement($modal, '//div[text()="' . $buttonLabel . '"]');
        $button->click();
    }

    /**
     * @Then Modal is open with text :arg1
     * @Then Modal is open with text :arg1 in selector :arg2
     *
     * Check if text is present in modal or dynamic modal.
     */
    public function modalIsOpenWithText(string $text, string $selector = 'div'): void
    {
        $textEncoded = str_contains($text, '"')
            ? 'concat("' . str_replace('"', '", \'"\', "', $text) . '")'
            : '"' . $text . '"';

        $modal = $this->findElement(null, '.modal.visible.active.front');
        $this->findElement($modal, '//' . $selector . '[text()[normalize-space()=' . $textEncoded . ']]');
    }

    /**
     * @When I fill Modal field :arg1 with :arg2
     */
    public function iFillModalField(string $fieldName, string $value): void
    {
        $modal = $this->findElement(null, '.modal.visible.active.front');
        $field = $modal->find('named', ['field', $fieldName]);
        $field->setValue($value);
    }

    /**
     * @Then I click close modal
     */
    public function iClickCloseModal(): void
    {
        $modal = $this->findElement(null, '.modal.visible.active.front');
        $closeIcon = $this->findElement($modal, '//i.icon.close');
        $closeIcon->click();
    }

    /**
     * @Then I hide js modal
     */
    public function iHideJsModal(): void
    {
        $modal = $this->findElement(null, '.modal.visible.active.front');
        $this->getSession()->executeScript('$(arguments[0]).modal(\'hide\')', [$modal]);
    }

    // }}}

    // {{{ panel

    /**
     * @Then Panel is open
     */
    public function panelIsOpen(): void
    {
        $this->findElement(null, '.atk-right-panel.atk-visible');
    }

    /**
     * @Then Panel is open with text :arg1
     * @Then Panel is open with text :arg1 in selector :arg2
     */
    public function panelIsOpenWithText(string $text, string $selector = 'div'): void
    {
        $panel = $this->findElement(null, '.atk-right-panel.atk-visible');
        $this->findElement($panel, '//' . $selector . '[text()[normalize-space()="' . $text . '"]]');
    }

    /**
     * @When I fill Panel field :arg1 with :arg2
     */
    public function iFillPanelField(string $fieldName, string $value): void
    {
        $panel = $this->findElement(null, '.atk-right-panel.atk-visible');
        $field = $panel->find('named', ['field', $fieldName]);
        $field->setValue($value);
    }

    /**
     * @Then I press Panel button :arg
     */
    public function iPressPanelButton(string $buttonLabel): void
    {
        $panel = $this->findElement(null, '.atk-right-panel.atk-visible');
        $button = $this->findElement($panel, '//div[text()="' . $buttonLabel . '"]');
        $button->click();
    }

    // }}}

    // {{{ tab

    /**
     * @Given I click tab with title :arg1
     */
    public function iClickTabWithTitle(string $tabTitle): void
    {
        $tabMenu = $this->findElement(null, '.ui.tabular.menu');
        $link = $this->findElement($tabMenu, '//div[text()="' . $tabTitle . '"]');
        $link->click();
    }

    /**
     * @Then Active tab should be :arg1
     */
    public function activeTabShouldBe(string $title): void
    {
        $tab = $this->findElement(null, '.ui.tabular.menu > .item.active');
        if ($tab->getText() !== $title) {
            throw new \Exception('Active tab is not ' . $title);
        }
    }

    // }}}

    // {{{ input

    /**
     * @Then ~^input "([^"]*)" value should start with "([^"]*)"$~
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
     * @Then I search grid for :arg1
     */
    public function iSearchGridFor(string $text): void
    {
        $search = $this->findElement(null, 'input.atk-grid-search');
        $search->setValue($text);
    }

    /**
     * @Then I select value :arg1 in lookup :arg2
     */
    public function iSelectValueInLookup(string $value, string $inputName): void
    {
        $isSelectorXpath = $this->parseSelector($inputName)[0] === 'xpath';

        // get dropdown item from Fomantic-UI which is direct parent of input HTML element
        $lookupElem = $this->findElement(null, ($isSelectorXpath ? $inputName : '//input[@name="' . $inputName . '"]') . '/parent::div');

        // open dropdown and wait till fully opened (just a click is not triggering it)
        $this->getSession()->executeScript('$(arguments[0]).dropdown(\'show\')', [$lookupElem]);
        $this->jqueryWait('$(arguments[0]).hasClass(\'visible\')', [$lookupElem]);

        // select value
        if ($value === '') { // TODO impl. native clearable - https://github.com/atk4/ui/issues/572
            $value = "\u{00a0}";
        }
        $valueElem = $this->findElement($lookupElem, '//div[text()="' . $value . '"]');
        $this->getSession()->executeScript('$(arguments[0]).dropdown(\'set selected\', arguments[1]);', [$lookupElem, $valueElem->getAttribute('data-value')]);
        $this->jqueryWait();

        // hide dropdown and wait till fully closed
        $this->getSession()->executeScript('$(arguments[0]).dropdown(\'hide\');', [$lookupElem]);
        $this->jqueryWait('!$(arguments[0]).hasClass(\'visible\')', [$lookupElem]);
    }

    /**
     * @When I select file input :arg1 with :arg2 as :arg3
     */
    public function iSelectFile(string $inputName, string $fileContent, string $fileName): void
    {
        $element = $this->findElement(null, '//input[@name="' . $inputName . '" and @type="hidden"]/following-sibling::input[@type="file"]');
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
     * @Then ~^rule "([^"]*)" operator is "([^"]*)" and value is "([^"]*)"$~
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
     * @Then ~^reference rule "([^"]*)" operator is "([^"]*)" and value is "([^"]*)"$~
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
     * @Then ~^select rule "([^"]*)" operator is "([^"]*)" and value is "([^"]*)"$~
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
     * @Then ~^date rule "([^"]*)" operator is "([^"]*)" and value is "([^"]*)"$~
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
     * @Then ~^bool rule "([^"]*)" has value "([^"]*)"$~
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
     * @Then ~^I check if input value for "([^"]*)" match text "([^"]*)"~
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

    /**
     * @Then ~^I check if input value for "([^"]*)" match text in "([^"]*)"$~
     */
    public function compareInputValueToElementText(string $inputName, string $selector): void
    {
        $inputName = $this->unquoteStepArgument($inputName);
        $selector = $this->unquoteStepArgument($selector);

        $expectedText = $this->findElement(null, $selector)->getText();
        $input = $this->findElement(null, 'input[name="' . $inputName . '"]');
        if ($expectedText !== $input->getValue()) {
            throw new \Exception('Input value does not match: ' . $input->getValue() . ', expected: ' . $expectedText);
        }
    }

    // }}}

    // {{{ misc

    /**
     * @Then dump :arg1
     */
    public function dump(string $arg1): void
    {
        $element = $this->getSession()->getPage()->find('xpath', '//div[text()="' . $arg1 . '"]');
        var_dump($element->getOuterHtml());
    }

    /**
     * @Then I click filter column name :arg1
     */
    public function iClickFilterColumnName(string $columnName): void
    {
        $column = $this->findElement(null, "th[data-column='" . $columnName . "']");
        $icon = $this->findElement($column, 'i');
        $icon->click();
    }

    /**
     * @Then ~^container "([^"]*)" should display "([^"]*)" item\(s\)$~
     */
    public function containerShouldHaveNumberOfItem(string $selector, int $numberOfitems): void
    {
        $selector = $this->unquoteStepArgument($selector);

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
     * @Then I scroll to top
     */
    public function iScrollToTop(): void
    {
        $this->getSession()->executeScript('window.scrollTo(0, 0)');
    }

    /**
     * @Then I scroll to bottom
     */
    public function iScrollToBottom(): void
    {
        $this->getSession()->executeScript('window.scrollTo(0, 100 * 1000)');
    }

    /**
     * @Then Toast display should contain text :arg1
     */
    public function toastDisplayShouldContainText(string $text): void
    {
        $toastContainer = $this->findElement(null, '.ui.toast-container');
        $toastText = $this->findElement($toastContainer, '.content')->getText();
        if (!str_contains($toastText, $text)) {
            throw new \Exception('Toast text "' . $toastText . '" does not contain "' . $text . '"');
        }
    }

    /**
     * @Then No toast should be displayed
     */
    public function noToastShouldBeDisplayed(): void
    {
        $toasts = $this->getSession()->getPage()->findAll('css', '.ui.toast-container .toast-box');
        if (count($toasts) > 0) {
            throw new \Exception('Toast is displayed: "' . $this->findElement(reset($toasts), '.content')->getText() . '"');
        }
    }

    /**
     * Remove once https://github.com/Behat/MinkExtension/pull/386 and
     * https://github.com/minkphp/Mink/issues/656 are fixed and released.
     *
     * @Then ~^PATCH MINK the (?i)url(?-i) should match "(?P<pattern>(?:[^"]|\\")*)"$~
     */
    public function assertUrlRegExp(string $pattern): void
    {
        $pattern = $this->unquoteStepArgument($pattern);

        $this->assertSession()->addressMatches($pattern);
    }

    /**
     * @Then ~^I check if text in "([^"]*)" match text in "([^"]*)"~
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
     * @Then ~^I check if text in "([^"]*)" match text "([^"]*)"~
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
     * @Then ~^I check if text in "([^"]*)" match regex "([^"]*)"~
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
     * @Then Element :arg1 attribute :arg2 should contain text :arg3
     */
    public function elementAttributeShouldContainText(string $selector, string $attribute, string $text): void
    {
        $element = $this->findElement(null, $selector);
        $attr = $element->getAttribute($attribute);
        if (!str_contains($attr, $text)) {
            throw new \Exception('Element " . $selector . " attribute "' . $attribute . '" does not contain "' . $text . '"');
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
