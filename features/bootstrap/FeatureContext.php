<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context
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
    protected $buttonId = null;

    public function getSession($name = null)
    {
        return $this->getMink()->getSession($name);
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
     * @Given I click link :arg1
     */
    public function iClickLink($arg1)
    {
        $link = $this->getSession()->getPage()->find('xpath', '//a[text()="' . $arg1 . '"]');
        $link->click();
    }

    /**
     * @Then I see button :arg1
     */
    public function iSee($arg1)
    {
        $element = $this->getSession()->getPage()->find('xpath', '//div[text()="' . $arg1 . '"]');
        if ($element->getAttribute('style')) {
            throw new \Exception("Element with text \"$arg1\" must be invisible");
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
        if (strpos('display: none', $element->getAttribute('style')) !== false) {
            throw new \Exception("Element with text \"$arg1\" must be invisible");
        }
    }

    /**
     * @Then Label changes to a number
     */
    public function labelChangesToANumber()
    {
        $element = $this->getSession()->getPage()->findById($this->buttonId);
        if (!is_numeric($element->getHtml())) {
            throw new \Exception('Label must be numeric');
        }
    }

    /**
     * @Then I press Modal button :arg
     * @param $arg
     *
     * @throws Exception
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
        if (!$text || $text->getText() != $arg1) {
            throw new \Exception('No such text in modal');
        }
    }

    /**
     * @Then Modal is showing text :arg1 inside tag :arg2
     * @param $arg1
     * @param $arg2
     *
     * @throws Exception
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
        if (!$text || $text->getText() != $arg1) {
            throw new \Exception('No such text in modal');
        }
    }

    /**
     * @Then Toast display should contains text :arg1
     * @param $arg1
     *
     * @throws Exception
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
        if (!$text || strpos($text->getText(), $arg1) === false) {
            throw new \Exception('No such text in toast');
        }
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
        if (!$value || $value->getText() != $arg1) {
            throw new \Exception('Value not found: ' . $arg1);
        }
        //When value are loaded, hide dropdown and select value from javascript.
        $script = '$("#' . $lookup->getAttribute('id') . '").dropdown("hide");';
        $script .= '$("#' . $lookup->getAttribute('id') . '").dropdown("set selected", ' . $value->getAttribute('data-value') . ');';
        $this->getSession()->executeScript($script);
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
     * @param int $duration The maximum time to wait for the function.
     */
    protected function jqueryWait($duration = 1000)
    {
        $this->getSession()->wait($duration, '(0 === jQuery.active && 0 === jQuery(\':animated\').length)');
        $this->getSession()->wait(300);
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

        if (strpos($field->getValue(), $arg2) === false) {
            throw new \Exception('Field value ' . $field->getValue() . ' does not start with ' . $arg2);
        }
    }
}
