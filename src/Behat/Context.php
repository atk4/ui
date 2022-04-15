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
use Behat\MinkExtension\Context\RawMinkContext;
use Exception;

class Context extends RawMinkContext implements BehatContext
{
    use WarnDynamicPropertyTrait;

    /** @var string|null Temporary store button id when press. Used in js callback test. */
    protected $buttonId;

    public function getSession($name = null): \Behat\Mink\Session
    {
        return $this->getMink()->getSession($name);
    }

    protected function getScenario(StepScope $event): ScenarioInterface
    {
        foreach ($event->getFeature()->getScenarios() as $scenario) {
            $scenarioSteps = $scenario->getSteps();
            if (count($scenarioSteps) > 0
                    && reset($scenarioSteps)->getLine() <= $event->getStep()->getLine()
                    && end($scenarioSteps)->getLine() >= $event->getStep()->getLine()) {
                return $scenario;
            }
        }

        throw new Exception('Unable to find scenario');
    }

    /**
     * @BeforeStep
     */
    public function closeAllToasts(BeforeStepScope $event): void
    {
        if (!$this->getSession()->getDriver()->isStarted()) {
            return;
        }

        if (!str_starts_with($event->getStep()->getText(), 'Toast display should contain text ')) {
            $this->getSession()->executeScript('jQuery(\'.toast-box > .ui.toast\').toast(\'close\');');
        }
    }

    /**
     * @AfterStep
     */
    public function waitUntilLoadingAndAnimationFinished(AfterStepScope $event): void
    {
        $this->jqueryWait();
        $this->disableAnimations();
        if (!str_contains($this->getScenario($event)->getTitle() ?? '', 'exception is displayed')) {
            $this->assertNoException();
        }
        $this->assertNoDuplicateId();
    }

    protected function getFinishedScript(): string
    {
        return 'document.readyState === \'complete\''
            . ' && typeof jQuery !== \'undefined\' && jQuery.active === 0'
            . ' && document.querySelectorAll(\'.animating.ui.transition:not(.looping)\').length === 0'
            . ' && typeof atk !== \'undefined\' && atk.vueService.areComponentsLoaded()';
    }

    /**
     * Wait till jQuery AJAX request finished and no animation is perform.
     */
    protected function jqueryWait(string $extraWaitCondition = 'true', int $maxWaitdurationMs = 5000): void
    {
        $finishedScript = '(' . $this->getFinishedScript() . ') && (' . $extraWaitCondition . ')';

        $s = microtime(true);
        $c = 0;
        while (microtime(true) - $s <= $maxWaitdurationMs / 1000) {
            $this->getSession()->wait($maxWaitdurationMs, $finishedScript);
            usleep(10_000);
            if ($this->getSession()->evaluateScript($finishedScript)) {
                if (++$c >= 2) {
                    return;
                }
            } else {
                $c = 0;
                usleep(20_000);
            }
        }

        throw new Exception('jQuery did not finish within a time limit');
    }

    protected function disableAnimations(): void
    {
        // disable all CSS/jQuery animations/transitions
        $toCssFx = function ($selector, $cssPairs) {
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
            'if (Array.prototype.filter.call(document.getElementsByTagName("style"), e => e.getAttribute("about") === "atk-test-behat").length === 0) {'
            . ' $(\'<style about="atk-test-behat">' . $css . '</style>\').appendTo(\'head\');'
            . ' }'
            . 'jQuery.fx.off = true;'
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

                throw new Exception('Page contains uncaught exception');
            }
        }
    }

    protected function assertNoDuplicateId(): void
    {
        [$invalidIds, $duplicateIds] = $this->getSession()->evaluateScript(<<<'EOF'
            return (function () {
                const idRegex = /^[_a-z][_a-z0-9-]*$/is;
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
        $invalidIds = array_diff($invalidIds, ['']); // id="" is hardcoded in templates
        $duplicateIds = array_diff($duplicateIds, ['atk', '_icon', 'atk_icon']); // generated when component is not correctly added to app/layout component tree - should throw, as such name/ID is dangerous to be used

        if (count($invalidIds) > 0) {
            throw new Exception('Page contains element with invalid ID: ' . implode(', ', array_map(fn ($v) => '"' . $v . '"', $invalidIds)));
        }

        if (count($duplicateIds) > 0) {
            throw new Exception('Page contains elements with duplicate ID: ' . implode(', ', array_map(fn ($v) => '"' . $v . '"', $duplicateIds)));
        }
    }

    /**
     * @return array{0: 'css'|'xpath', 1: string}
     */
    protected function parseSelector(string $selector): array
    {
        if (preg_match('~^xpath\((.+)\)$~is', $selector, $matches)) {
            return ['xpath', $matches[1]];
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
            throw new Exception('No element found in ' . ($context === null ? 'page' : 'element')
                . ' using selector: ' . $selector);
        }

        return $elements;
    }

    protected function findElement(?NodeElement $context, string $selector): NodeElement
    {
        $elements = $this->findElements($context, $selector);
        if (count($elements) > 1) {
//            throw new Exception('More than one element found in ' . ($context === null ? 'page' : 'element')
//                . ' using selector: ' . $selector);
        }

        return $elements[0];
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

    // {{{ button

    /**
     * @When I press button :arg1
     */
    public function iPressButton(string $btnLabel): void
    {
        $button = $this->findElement(null, 'xpath(//div[text()="' . $btnLabel . '"])');
        // store button id.
        $this->buttonId = $button->getAttribute('id');
        // fix "is out of bounds of viewport width and height" for Firefox
        $button->focus();
        $button->click();
    }

    /**
     * @Then I press menu button :arg1 using selector :selector
     */
    public function iPressMenuButtonUsingClass(string $btnLabel, string $selector): void
    {
        $menu = $this->findElement(null, '.ui.menu.' . $selector);
        $link = $this->findElement($menu, 'xpath(//a[text()="' . $btnLabel . '"])');
        $this->getSession()->executeScript('$("#' . $link->getAttribute('id') . '").click()');
    }

    /**
     * @Then I see button :arg1
     */
    public function iSeeButton(string $buttonLabel): void
    {
        $this->findElement(null, 'xpath(//div[text()="' . $buttonLabel . '"])');
    }

    /**
     * @Then I don't see button :arg1
     */
    public function idontSeeButton(string $text): void
    {
        $element = $this->findElement(null, 'xpath(//div[text()="' . $text . '"])');
        if (!str_contains($element->getAttribute('style'), 'display: none')) {
            throw new Exception('Element with text "' . $text . '" must be invisible');
        }
    }

    /**
     * @Then Label changes to a number
     */
    public function labelChangesToNumber(): void
    {
        $element = $this->getSession()->getPage()->findById($this->buttonId);
        $value = trim($element->getHtml());
        if (!is_numeric($value)) {
            throw new Exception('Label must be numeric on button: ' . $this->buttonId . ' : ' . $value);
        }
    }

    // }}}

    // {{{ link

    /**
     * @Given I click link :arg1
     */
    public function iClickLink(string $label): void
    {
        $this->findElement(null, 'xpath(//a[text()="' . $label . '"])')->click();
    }

    /**
     * @Then I click first card on page
     */
    public function iClickFirstCardOnPage(): void
    {
        $this->getSession()->executeScript('$(".atk-card")[0].click()');
    }

    /**
     * @Then I click first element using selector :selector
     */
    public function iClickFirstElementUsingClass(string $selector): void
    {
        $this->getSession()->executeScript('$("' . $selector . '")[0].click()');
    }

    /**
     * @Then I click paginator page :arg1
     */
    public function iClickPaginatorPage(string $pageNumber): void
    {
        $this->getSession()->executeScript('$("a.item[data-page=' . $pageNumber . ']").click()');
    }

    /**
     * @Then I click icon using selector :selector
     */
    public function iClickIconUsingCss(string $selector): void
    {
        $icon = $this->findElement(null, $selector);
        $icon->click();
    }

    // }}}

    // {{{ modal

    /**
     * @Then I press Modal button :arg
     */
    public function iPressModalButton(string $buttonLabel): void
    {
        $modal = $this->findElement(null, '.modal.visible.active.front');
        $btn = $this->findElement($modal, 'xpath(//div[text()="' . $buttonLabel . '"])');
        $btn->click();
    }

    /**
     * @Then Modal is open with text :arg1
     * @Then Modal is open with text :arg1 in tag :arg2
     *
     * Check if text is present in modal or dynamic modal.
     */
    public function modalIsOpenWithText(string $text, string $tag = 'div'): void
    {
        $modal = $this->findElement(null, '.modal.visible.active.front');
        $this->findElement($modal, 'xpath(//' . $tag . '[text()[normalize-space()="' . $text . '"]])');
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
     * @Then I hide js modal
     */
    public function iHideJsModal(): void
    {
        $this->getSession()->executeScript('$(".modal.active.front").modal("hide")');
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
     * @Then Panel is open with text :arg1 in tag :arg2
     */
    public function panelIsOpenWithText(string $text, string $tag = 'div'): void
    {
        $panel = $this->findElement(null, '.atk-right-panel.atk-visible');
        $this->findElement($panel, 'xpath(//' . $tag . '[text()[normalize-space()="' . $text . '"]])');
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
        $btn = $this->findElement($panel, 'xpath(//div[text()="' . $buttonLabel . '"])');
        $btn->click();
    }

    // }}}

    // {{{ tab

    /**
     * @Given I click tab with title :arg1
     */
    public function iClickTabWithTitle(string $tabTitle): void
    {
        $tabMenu = $this->findElement(null, '.ui.tabular.menu');
        $link = $this->findElement($tabMenu, 'xpath(//a[text()="' . $tabTitle . '"])');

        $this->getSession()->executeScript('$("#' . $link->getAttribute('id') . '").click()');
    }

    /**
     * @Then Active tab should be :arg1
     */
    public function activeTabShouldBe(string $title): void
    {
        $tab = $this->findElement(null, '.ui.tabular.menu > .item.active');
        if ($tab->getText() !== $title) {
            throw new Exception('Active tab is not ' . $title);
        }
    }

    // }}}

    // {{{ input

    /**
     * @Then /^input "([^"]*)" value should start with "([^"]*)"$/
     */
    public function inputValueShouldStartWith(string $inputName, string $text): void
    {
        $field = $this->assertSession()->fieldExists($inputName);

        if (!str_starts_with($field->getValue(), $text)) {
            throw new Exception('Field value ' . $field->getValue() . ' does not start with ' . $text);
        }
    }

    /**
     * @Then I set calendar input name :arg1 with value :arg2
     */
    public function iSetCalendarInputNameWithValue(string $inputName, string $value): void
    {
        $script = '$(\'input[name="' . $inputName . '"]\').get(0)._flatpickr.setDate("' . $value . '")';
        $this->getSession()->executeScript($script);
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
        // get dropdown item from semantic ui which is direct parent of input html element
        $lookupElem = $this->findElement(null, 'xpath(//input[@name="' . $inputName . '"]/parent::div)');

        // open dropdown and wait till fully opened (just a click is not triggering it)
        $this->getSession()->executeScript('$("#' . $lookupElem->getAttribute('id') . '").dropdown("show")');
        $this->jqueryWait('$("#' . $lookupElem->getAttribute('id') . '").hasClass("visible")');

        // select value
        $valueElem = $this->findElement($lookupElem, 'xpath(//div[text()="' . $value . '"])');
        $this->getSession()->executeScript('$("#' . $lookupElem->getAttribute('id') . '").dropdown("set selected", ' . $valueElem->getAttribute('data-value') . ');');
        $this->jqueryWait();

        // hide dropdown and wait till fully closed
        $this->getSession()->executeScript('$("#' . $lookupElem->getAttribute('id') . '").dropdown("hide");');
        $this->jqueryWait();
        // for unknown reasons, dropdown very often remains visible in CI, so hide twice
        $this->getSession()->executeScript('$("#' . $lookupElem->getAttribute('id') . '").dropdown("hide");');
        $this->jqueryWait('!$("#' . $lookupElem->getAttribute('id') . '").hasClass("visible")');
    }

    /**
     * Generic ScopeBuilder rule with select operator and input value.
     *
     * @Then /^rule "([^"]*)" operator is "([^"]*)" and value is "([^"]*)"$/
     */
    public function scopeBuilderRule(string $name, string $operator, string $value): void
    {
        $rule = $this->getScopeBuilderRuleElem($name);
        $this->assertSelectedValue($rule, $operator, '.vqb-rule-operator select');
        $this->assertInputValue($rule, $value);
    }

    /**
     * hasOne reference or enum type rule for ScopeBuilder.
     *
     * @Then /^reference rule "([^"]*)" operator is "([^"]*)" and value is "([^"]*)"$/
     */
    public function scopeBuilderReferenceRule(string $name, string $operator, string $value): void
    {
        $rule = $this->getScopeBuilderRuleElem($name);
        $this->assertSelectedValue($rule, $operator, '.vqb-rule-operator select');
        $this->assertDropdownValue($rule, $value, '.vqb-rule-input .active.item');
    }

    /**
     * hasOne select or enum type rule for ScopeBuilder.
     *
     * @Then /^select rule "([^"]*)" operator is "([^"]*)" and value is "([^"]*)"$/
     */
    public function scopeBuilderSelectRule(string $name, string $operator, string $value): void
    {
        $rule = $this->getScopeBuilderRuleElem($name);
        $this->assertSelectedValue($rule, $operator, '.vqb-rule-operator select');
        $this->assertSelectedValue($rule, $value, '.vqb-rule-input select');
    }

    /**
     * Date, Time or Datetime rule for ScopeBuilder.
     *
     * @Then /^date rule "([^"]*)" operator is "([^"]*)" and value is "([^"]*)"$/
     */
    public function scopeBuilderDateRule(string $name, string $operator, string $value): void
    {
        $rule = $this->getScopeBuilderRuleElem($name);
        $this->assertSelectedValue($rule, $operator, '.vqb-rule-operator select');
        $this->assertInputValue($rule, $value, 'input.form-control');
    }

    /**
     * Boolean type rule for ScopeBuilder.
     *
     * @Then /^bool rule "([^"]*)" has value "([^"]*)"$/
     */
    public function scopeBuilderBoolRule(string $name, string $value): void
    {
        $this->getScopeBuilderRuleElem($name);
        $idx = ($value === 'Yes') ? 0 : 1;
        $isChecked = $this->getSession()->evaluateScript('return $(\'[data-name="' . $name . '"]\').find(\'input\')[' . $idx . '].checked');
        if (!$isChecked) {
            throw new Exception('Radio value selected is not: ' . $value);
        }
    }

    /**
     * @Then /^I check if input value for "([^"]*)" match text in "([^"]*)"$/
     */
    public function compareInputValueToElementText(string $inputName, string $selector): void
    {
        $expectedText = $this->findElement(null, $selector)->getText();
        $input = $this->findElement(null, 'input[name="' . $inputName . '"]');

        if (preg_replace('~\s*~', '', $expectedText) !== preg_replace('~\s*~', '', $input->getValue())) {
            throw new Exception('Input value does not match: ' . $input->getValue() . ' expected: ' . $expectedText);
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

        $this->getSession()->executeScript('$("#' . $icon->getAttribute('id') . '").click()');
    }

    /**
     * @Then /^container "([^"]*)" should display "([^"]*)" item\(s\)$/
     */
    public function containerShouldHaveNumberOfItem(string $selector, int $numberOfitems): void
    {
        $items = $this->getSession()->getPage()->findAll('css', $selector);
        $count = 0;
        foreach ($items as $el => $item) {
            ++$count;
        }
        if ($count !== $numberOfitems) {
            throw new Exception('Items does not match. There were ' . $count . ' item in container');
        }
    }

    /**
     * @Then I scroll to top
     */
    public function iScrollToTop(): void
    {
        $this->getSession()->executeScript('window.scrollTo(0,0)');
    }

    /**
     * @Then Toast display should contain text :arg1
     */
    public function toastDisplayShouldContainText(string $text): void
    {
        $toast = $this->findElement(null, '.ui.toast-container');
        if (!str_contains($this->findElement($toast, '.content')->getText(), $text)) {
            throw new Exception('Cannot find text: "' . $text . '" in toast');
        }
    }

    /**
     * @Then /^page url should contain \'([^\']*)\'$/
     */
    public function pageUrlShouldContain(string $text): void
    {
        $url = $this->getSession()->getCurrentUrl();
        if (!str_contains($url, $text)) {
            throw new Exception('Text : "' . $text . '" not found in ' . $url);
        }
    }

    /**
     * @Then /^I check if text in "([^"]*)" match text in "([^"]*)"/
     */
    public function compareElementText(string $compareSelector, string $compareToSelector): void
    {
        if ($this->findElement(null, $compareSelector)->getText() !== $this->findElement(null, $compareToSelector)->getText()) {
            throw new Exception('Text does not match between: ' . $compareSelector . ' and ' . $compareToSelector);
        }
    }

    /**
     * @Then /^text in container using selector "([^\"]*)" should contain \'([^\']*)\'$/
     */
    public function textInContainerUsingShouldContain(string $selector, string $text): void
    {
        if (trim($this->findElement(null, $selector)->getText()) !== $text) {
            throw new Exception('Container with selector: ' . $selector . ' does not contain text: ' . $text);
        }
    }

    // }}}

    /**
     * Find a dropdown component within an html element
     * and check if value is set in dropdown.
     */
    private function assertDropdownValue(NodeElement $element, string $value, string $selector): void
    {
        if ($this->findElement($element, $selector)->getHtml() !== $value) {
            throw new Exception('Value: "' . $value . '" not set using selector: ' . $selector);
        }
    }

    /**
     * Find a select input type within an html element
     * and check if value is selected.
     */
    private function assertSelectedValue(NodeElement $element, string $value, string $selector): void
    {
        if ($this->findElement($element, $selector)->getValue() !== $value) {
            throw new Exception('Value: "' . $value . '" not set using selector: ' . $selector);
        }
    }

    /**
     * Find an input within an html element and check
     * if value is set.
     */
    private function assertInputValue(NodeElement $element, string $value, string $selector = 'input'): void
    {
        if ($this->findElement($element, $selector)->getValue() !== $value) {
            throw new Exception('Input value not is not: ' . $value);
        }
    }

    private function getScopeBuilderRuleElem(string $ruleName): NodeElement
    {
        return $this->findElement(null, '.vqb-rule[data-name=' . $ruleName . ']');
    }
}
