<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Atk4\Core\WarnDynamicPropertyTrait;
use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
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

    /**
     * @BeforeStep
     */
    public function closeAllToasts(BeforeStepScope $event): void
    {
        if (!$this->getSession()->getDriver()->isStarted()) {
            return;
        }

        if (strpos($event->getStep()->getText(), 'Toast display should contains text ') !== 0) {
            $this->getSession()->executeScript('$(\'.toast-box > .ui.toast\').toast(\'close\');');
        }
    }

    /**
     * @AfterStep
     */
    public function waitUntilLoadingAndAnimationFinished(AfterStepScope $event): void
    {
        $this->jqueryWait();
        $this->disableAnimations();
        $this->assertNoException();
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
            usleep(10000);
            if ($this->getSession()->evaluateScript($finishedScript)) {
                if (++$c >= 2) {
                    return;
                }
            } else {
                $c = 0;
                usleep(50000);
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

    protected function getElementInPage(string $selector, string $method = 'css'): NodeElement
    {
        $element = $this->getSession()->getPage()->find($method, $selector);
        if ($element === null) {
            throw new Exception('Could not get element in page using this selector: ' . $selector);
        }

        return $element;
    }

    protected function getElementInElement(NodeElement $element, string $selector, string $method = 'css'): NodeElement
    {
        $find = $element->find($method, $selector);
        if ($find === null) {
            throw new Exception('Could not get element in element using this selector: ' . $selector);
        }

        return $find;
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
        $button = $this->getElementInPage('//div[text()="' . $btnLabel . '"]', 'xpath');
        // store button id.
        $this->buttonId = $button->getAttribute('id');
        // fix "is out of bounds of viewport width and height" for Firefox
        $button->focus();
        $button->click();
    }

    /**
     * @Then I press menu button :arg1 using class :arg2
     */
    public function iPressMenuButtonUsingClass(string $btnLabel, string $selector): void
    {
        $menu = $this->getElementInPage('.ui.menu.' . $selector);
        $link = $this->getElementInElement($menu, '//a[text()="' . $btnLabel . '"]', 'xpath');
        $this->getSession()->executeScript('$("#' . $link->getAttribute('id') . '").click()');
    }

    /**
     * @Then I see button :arg1
     */
    public function iSee(string $buttonLabel): void
    {
        $this->getElementInPage('//div[text()="' . $buttonLabel . '"]', 'xpath');
    }

    /**
     * @Then I don't see button :arg1
     */
    public function elementIsHide(string $text): void
    {
        $element = $this->getElementInPage('//div[text()="' . $text . '"]', 'xpath');
        if (mb_strpos('display: none', $element->getAttribute('style')) !== false) {
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
        $this->getElementInPage('//a[text()="' . $label . '"]', 'xpath')->click();
    }

    /**
     * @Then I click first card on page
     */
    public function iClickFirstCardOnPage(): void
    {
        $this->getSession()->executeScript('$(".atk-card")[0].click()');
    }

    /**
     * @Then I click first element using class :arg1
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
     * @Then I click icon using css :arg1
     */
    public function iClickIconUsingCss(string $selector): void
    {
        $icon = $this->getElementInPage($selector);
        $icon->click();
    }

    // }}}

    // {{{ modal

    /**
     * @Then I press Modal button :arg
     */
    public function iPressModalButton(string $buttonLabel): void
    {
        $modal = $this->getElementInPage('.modal.visible.active.front');
        $btn = $this->getElementInElement($modal, '//div[text()="' . $buttonLabel . '"]', 'xpath');
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
        $modal = $this->getElementInPage('.modal.visible.active.front');
        $this->getElementInElement($modal, '//' . $tag . '[text()="' . $text . '"]', 'xpath');
    }

    /**
     * @When I fill Modal field :arg1 with :arg2
     */
    public function iFillModalField(string $fieldName, string $value): void
    {
        $modal = $this->getElementInPage('.modal.visible.active.front');
        $field = $modal->find('named', ['field', $fieldName]);
        $field->setValue($value);
    }

    /**
     * @Then I hide js modal
     *
     * Hide js modal.
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
        $this->getElementInPage('.atk-right-panel.atk-visible');
    }

    /**
     * @Then Panel is open with text :arg1
     * @Then Panel is open with text :arg1 in tag :arg2
     */
    public function panelIsOpenWithText(string $text, string $tag = 'div'): void
    {
        $panel = $this->getElementInPage('.atk-right-panel.atk-visible');
        $this->getElementInElement($panel, '//' . $tag . '[text()="' . $text . '"]', 'xpath');
    }

    /**
     * @When I fill Panel field :arg1 with :arg2
     */
    public function iFillPanelField(string $fieldName, string $value): void
    {
        $panel = $this->getElementInPage('.atk-right-panel.atk-visible');
        $field = $panel->find('named', ['field', $fieldName]);
        $field->setValue($value);
    }

    /**
     * @Then I press Panel button :arg
     */
    public function iPressPanelButton(string $buttonLabel): void
    {
        $panel = $this->getElementInPage('.atk-right-panel.atk-visible');
        $btn = $this->getElementInElement($panel, '//div[text()="' . $buttonLabel . '"]', 'xpath');
        $btn->click();
    }

    // }}}

    // {{{ tab

    /**
     * @Given I click tab with title :arg1
     */
    public function iClickTabWithTitle(string $tabTitle): void
    {
        $tabMenu = $this->getElementInPage('.ui.tabular.menu');
        $link = $this->getElementInElement($tabMenu, '//a[text()="' . $tabTitle . '"]', 'xpath');

        $this->getSession()->executeScript('$("#' . $link->getAttribute('id') . '").click()');
    }

    /**
     * @Then Active tab should be :arg1
     */
    public function activeTabShouldBe(string $title): void
    {
        $tab = $this->getElementInPage('.ui.tabular.menu > .item.active');
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

        if (mb_strpos($field->getValue(), $text) === false) {
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
        $search = $this->getElementInPage('input.atk-grid-search');
        $search->setValue($text);
    }

    /**
     * @Then I select value :arg1 in lookup :arg2
     *
     * Select a value in a lookup control.
     */
    public function iSelectValueInLookup(string $value, string $inputName): void
    {
        // get dropdown item from semantic ui which is direct parent of input html element
        $inputElem = $this->getElementInPage('input[name=' . $inputName . ']');
        $lookupElem = $inputElem->getParent();

        // open dropdown and wait till fully opened (just a click is not triggering it)
        $this->getSession()->executeScript('$("#' . $lookupElem->getAttribute('id') . '").dropdown("show")');
        $this->jqueryWait('$("#' . $lookupElem->getAttribute('id') . '").hasClass("visible")');

        // select value
        $valueElem = $this->getElementInElement($lookupElem, '//div[text()="' . $value . '"]', 'xpath');
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
        $expectedText = $this->getElementInPage($selector)->getText();
        $input = $this->getElementInPage('input[name="' . $inputName . '"]');

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
        $column = $this->getElementInPage("th[data-column='" . $columnName . "']");
        $icon = $this->getElementInElement($column, 'i');

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
     * @Then Toast display should contains text :arg1
     */
    public function toastDisplayShouldContainText(string $text): void
    {
        $toast = $this->getElementInPage('.ui.toast-container');
        if (mb_strpos($this->getElementInElement($toast, '.content')->getText(), $text) === false) {
            throw new Exception('Cannot find text: "' . $text . '" in toast');
        }
    }

    /**
     * @Then /^page url should contains \'([^\']*)\'$/
     */
    public function pageUrlShouldContains(string $text): void
    {
        $url = $this->getSession()->getCurrentUrl();
        if (!strpos($url, $text)) {
            throw new Exception('Text : "' . $text . '" not found in ' . $url);
        }
    }

    /**
     * @Then /^I check if text in "([^"]*)" match text in "([^"]*)"/
     */
    public function compareElementText(string $compareSelector, string $compareToSelector): void
    {
        if ($this->getElementInPage($compareSelector)->getText() !== $this->getElementInPage($compareToSelector)->getText()) {
            throw new Exception('Text does not match between: ' . $compareSelector . ' and ' . $compareToSelector);
        }
    }

    /**
     * @Then /^text in container using \'([^\']*)\' should contains \'([^\']*)\'$/
     */
    public function textInContainerUsingShouldContains(string $selector, string $text): void
    {
        if (trim($this->getElementInPage($selector)->getText()) !== $text) {
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
        if ($this->getElementInElement($element, $selector)->getHtml() !== $value) {
            throw new Exception('Value: "' . $value . '" not set using selector: ' . $selector);
        }
    }

    /**
     * Find a select input type within an html element
     * and check if value is selected.
     */
    private function assertSelectedValue(NodeElement $element, string $value, string $selector): void
    {
        if ($this->getElementInElement($element, $selector)->getValue() !== $value) {
            throw new Exception('Value: "' . $value . '" not set using selector: ' . $selector);
        }
    }

    /**
     * Find an input within an html element and check
     * if value is set.
     */
    private function assertInputValue(NodeElement $element, string $value, string $selector = 'input'): void
    {
        if ($this->getElementInElement($element, $selector)->getValue() !== $value) {
            throw new Exception('Input value not is not: ' . $value);
        }
    }

    private function getScopeBuilderRuleElem(string $ruleName): NodeElement
    {
        return $this->getElementInPage('.vqb-rule[data-name=' . $ruleName . ']');
    }
}
