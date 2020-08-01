<?php

declare(strict_types=1);

namespace atk4\ui\behat;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContextBasic extends RawMinkContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /** @var null Temporary store button id when press. Use in js callback test. */
    protected $buttonId;

    public function getSession($name = null): \Behat\Mink\Session
    {
        return $this->getMink()->getSession($name);
    }

    /**
     * Sleep for a certain time in ms.
     *
     * @Then I sleep :arg1 ms
     *
     * @param $arg1
     */
    public function iSleep($arg1)
    {
        $this->getSession()->wait($arg1);
    }

    /**
     * @When form submits
     */
    public function formSubmits()
    {
        $this->jqueryWait(20000);
    }

    /**
     * @When wait for callback
     */
    public function waitForCallback()
    {
        $this->jqueryWait(20000);
    }

    /**
     * @When I press button :arg1
     */
    public function iPressButton($arg1)
    {
        $button = $this->getSession()->getPage()->find('xpath', '//div[text()="' . $arg1 . '"]');
        // store button id.
        $this->buttonId = $button->getAttribute('id');
        $button->click();
    }

    /**
     * @Then I press menu button :arg1 using class :arg2
     */
    public function iPressMenuButtonUsingClass($arg1, $arg2)
    {
        $menu = $this->getSession()->getPage()->find('css', '.ui.menu.' . $arg2);
        if (!$menu) {
            throw new \Exception('Unable to find a menu with class ' . $arg2);
        }

        $link = $menu->find('xpath', '//a[text()="' . $arg1 . '"]');
        if (!$link) {
            throw new \Exception('Unable to find menu with title ' . $arg1);
        }

        $script = '$("#' . $link->getAttribute('id') . '").click()';
        $this->getSession()->executeScript($script);
    }

    /**
     * @Given I click link :arg1
     */
    public function iClickLink($arg1)
    {
        $link = $this->getSession()->getPage()->find('xpath', '//a[text()="' . $arg1 . '"]');
        $link->click();
    }

    /**
     * @Then I click filter column name :arg1
     */
    public function iClickFilterColumnName($arg1)
    {
        $column = $this->getSession()->getPage()->find('css', "th[data-column='" . $arg1 . "']");
        if (!$column) {
            throw new \Exception('Unable to find a column ' . $arg1);
        }
        $icon = $column->find('css', 'i');
        if (!$icon) {
            throw new \Exception('Column does not contain clickable icon.');
        }
        $script = '$("#' . $icon->getAttribute('id') . '").click()';
        $this->getSession()->executeScript($script);
    }

    /**
     * @Given I click tab with title :arg1
     *
     * @param $arg1
     */
    public function iClickTabWithTitle($arg1)
    {
        $tabMenu = $this->getSession()->getPage()->find('css', '.ui.tabular.menu');
        if (!$tabMenu) {
            throw new \Exception('Unable to find a tab menu.');
        }

        $link = $tabMenu->find('xpath', '//a[text()="' . $arg1 . '"]');
        if (!$link) {
            throw new \Exception('Unable to find tab with title ' . $arg1);
        }

        $script = '$("#' . $link->getAttribute('id') . '").click()';
        $this->getSession()->executeScript($script);
    }

    /**
     * @Then I click first card on page
     */
    public function iClickFirstCardOnPage()
    {
        $script = '$(".atk-card")[0].click()';
        $this->getSession()->executeScript($script);
    }

    /**
     * @Then I click first element using class :arg1
     */
    public function iClickFirstElementUsingClass($arg1)
    {
        $script = '$("' . $arg1 . '")[0].click()';
        $this->getSession()->executeScript($script);
    }

    /**
     * @Then I click paginator page :arg1
     */
    public function iClickPaginatorPage($arg1)
    {
        $script = '$("a.item[data-page=' . $arg1 . ']").click()';
        $this->getSession()->executeScript($script);
    }

    /**
     * @Then I see button :arg1
     */
    public function iSee($arg1)
    {
        $element = $this->getSession()->getPage()->find('xpath', '//div[text()="' . $arg1 . '"]');
        if ($element->getAttribute('style')) {
            throw new \Exception("Element with text \"{$arg1}\" must be invisible");
        }
    }

    /**
     * @Then dump :arg1
     */
    public function dump($arg1)
    {
        $element = $this->getSession()->getPage()->find('xpath', '//div[text()="' . $arg1 . '"]');
        var_dump($element->getOuterHtml());
    }

    /**
     * @Then I don't see button :arg1
     */
    public function iDontSee($arg1)
    {
        $element = $this->getSession()->getPage()->find('xpath', '//div[text()="' . $arg1 . '"]');
        if (mb_strpos('display: none', $element->getAttribute('style')) !== false) {
            throw new \Exception("Element with text \"{$arg1}\" must be invisible");
        }
    }

    /**
     * @Then Label changes to a number
     */
    public function labelChangesToNumber()
    {
        $this->getSession()->wait(5000, '!$("#' . $this->buttonId . '").hasClass("loading")');
        $element = $this->getSession()->getPage()->findById($this->buttonId);
        $value = trim($element->getHtml());
        if (!is_numeric($value)) {
            throw new \Exception('Label must be numeric on button: ' . $this->buttonId . ' : ' . $value);
        }
    }

    /**
     * @Then I press Modal button :arg
     *
     * @param $arg
     */
    public function iPressModalButton($arg)
    {
        $modal = $this->getSession()->getPage()->find('css', '.modal.transition.visible.active.front');
        if ($modal === null) {
            throw new \Exception('No modal found');
        }
        //find button in modal
        $btn = $modal->find('xpath', '//div[text()="' . $arg . '"]');
        if (!$btn) {
            throw new \Exception('Cannot find button in modal');
        }
        $btn->click();
    }

    /**
     * @Then Modal is open with text :arg1
     *
     * Check if text is present in modal or dynamic modal.
     */
    public function modalIsOpenWithText($arg1)
    {
        //get modal
        $modal = $this->getSession()->getPage()->find('css', '.modal.transition.visible.active.front');
        if ($modal === null) {
            throw new \Exception('No modal found');
        }
        //find text in modal
        $text = $modal->find('xpath', '//div[text()="' . $arg1 . '"]');
        if (!$text || $text->getText() !== $arg1) {
            throw new \Exception('No such text in modal');
        }
    }

    /**
     * @Then Active tab should be :arg1
     */
    public function activeTabShouldBe($arg1)
    {
        $tab = $this->getSession()->getPage()->find('css', '.ui.tabular.menu > .item.active');
        if ($tab->getText() !== $arg1) {
            throw new \Exception('Active tab is not ' . $arg1);
        }
    }

    /**
     * @Then Modal is showing text :arg1 inside tag :arg2
     *
     * @param $arg1
     * @param $arg2
     */
    public function modalIsShowingText($arg1, $arg2)
    {
        //get modal
        $modal = $this->getSession()->getPage()->find('css', '.modal.transition.visible.active.front');
        if ($modal === null) {
            throw new \Exception('No modal found');
        }
        //find text in modal
        $text = $modal->find('xpath', '//' . $arg2 . '[text()="' . $arg1 . '"]');
        if (!$text || $text->getText() !== $arg1) {
            throw new \Exception('No such text in modal');
        }
    }

    /**
     * @Then I hide js modal
     *
     * Hide js modal.
     */
    public function iHideJsModal()
    {
        $script = '$(".modal.active.front").modal("hide")';
        $this->getSession()->executeScript($script);
    }

    /**
     * @Then I scroll to top
     */
    public function iScrollToTop()
    {
        $script = 'window.scrollTo(0,0)';
        $this->getSession()->executeScript($script);
    }

    /**
     * @Then Toast display should contains text :arg1
     *
     * @param $arg1
     */
    public function toastDisplayShouldContainText($arg1)
    {
        //get toast
        $toast = $this->getSession()->getPage()->find('css', '.ui.toast-container');
        if ($toast === null) {
            throw new \Exception('No toast found');
        }
        $content = $toast->find('css', '.content');
        if ($content === null) {
            throw new \Exception('No Content in Toast');
        }
        //find text in toast
        $text = $content->find('xpath', '//div');
        if (!$text || mb_strpos($text->getText(), $arg1) === false) {
            throw new \Exception('No such text in toast');
        }
    }

    /**
     * @Then I wait for toast to hide
     */
    public function iWaitForToastToHide()
    {
        $this->getSession()->wait(20000, '$(".ui.toast-container").children().length === 0');
    }

    /**
     * @Then I select value :arg1 in lookup :arg2
     *
     * Select a value in a lookup field.
     */
    public function iSelectValueInLookup($arg1, $arg2)
    {
        $field = $this->getSession()->getPage()->find('css', 'input[name=' . $arg2 . ']');
        if ($field === null) {
            throw new \Exception('Field not found: ' . $arg2);
        }
        //get dropdown item from semantic ui which is direct parent of input name field.
        $lookup = $field->getParent();

        //open dropdown from semantic-ui command. (just a click is not triggering it)
        $script = '$("#' . $lookup->getAttribute('id') . '").dropdown("show")';
        $this->getSession()->executeScript($script);
        //Wait till dropdown is visible
        //Cannot call jqueryWait because calling it will return prior from dropdown to fire ajax request.
        $this->getSession()->wait(20000, '$("#' . $lookup->getAttribute('id') . '").hasClass("visible")');
        //value should be available.
        $value = $lookup->find('xpath', '//div[text()="' . $arg1 . '"]');
        if (!$value || $value->getText() !== $arg1) {
            throw new \Exception('Value not found: ' . $arg1);
        }
        //When value are loaded, hide dropdown and select value from javascript.
        $script = '$("#' . $lookup->getAttribute('id') . '").dropdown("hide");';
        $script .= '$("#' . $lookup->getAttribute('id') . '").dropdown("set selected", ' . $value->getAttribute('data-value') . ');';
        $this->getSession()->executeScript($script);
    }

    /**
     * @Then I search grid for :arg1
     */
    public function iSearchGridFor($arg1)
    {
        $search = $this->getSession()->getPage()->find('css', 'input.atk-grid-search');
        if (!$search) {
            throw new \Exception('Unable to find search input.');
        }

        $search->setValue($arg1);
    }

    /**
     * @Then I click icon using css :arg1
     */
    public function iClickIconUsingCss($arg1)
    {
        $icon = $this->getSession()->getPage()->find('css', $arg1);
        if (!$icon) {
            throw new \Exception('Unable to find search remove icon.');
        }

        $icon->click();
    }

    /**
     * Wait for an element, usually an auto trigger element, to show that loading has start"
     * Example, when entering value in JsSearch for grid. We need to auto trigger to fire before
     * doing waiting for callback.
     * $arg1 should represent the element selector for jQuery.
     *
     * @Then I wait for loading to start in :arg1
     */
    public function iWaitForLoadingToStartIn($arg1)
    {
        $this->getSession()->wait(2000, '$("' . $arg1 . '").hasClass("loading")');
    }

    /**
     * @Then I test javascript
     */
    public function iTestJavascript()
    {
        $title = $this->getSession()->evaluateScript('return window.document.title;');
        echo 'I\'m correctly on the webpage entitled "' . $title . '"';
    }

    /**
     * Wait till jquery ajax request finished and no animation is perform.
     *
     * @param int $duration the maximum time to wait for the function
     */
    protected function jqueryWait($duration = 1000)
    {
        $this->getSession()->wait($duration, '(0 === jQuery.active && 0 === jQuery(\':animated\').length)');
        $this->getSession()->wait(500);
    }

    /**
     * @Then /^the "([^"]*)"  should start with "([^"]*)"$/
     */
    public function theShouldStartWith($arg1, $arg2)
    {
        $field = $this->assertSession()->fieldExists($arg1);

        if (!$field) {
            throw new \Exception('Field' . $arg1 . ' does not exist');
        }

        if (mb_strpos($field->getValue(), $arg2) === false) {
            throw new \Exception('Field value ' . $field->getValue() . ' does not start with ' . $arg2);
        }
    }
}
