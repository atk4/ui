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

    protected $button = null;

    public function getSession($name = null)
    {
        return $this->getMink()->getSession($name);
    }

    /**
     * @When I use form with button :arg1
     */
    public function iUseFormWithButton($arg1)
    {
        $button = $this->getSession()->getPage()->find('xpath', '//button[text()="'.$arg1.'"]');
        $this->button_id = $button->getAttribute('id');
        $button->click();
    }

    /**
     * @When form submits
     */
    public function formSubmits()
    {
        $button = $this->getSession()->wait(5000, "$('.form.success').not('.loading').length");
    }

    /**
     * @When Wait until loading stops
     */
    public function untilLoadingStops()
    {
        $button = $this->getSession()->wait(5000, "! $('.ui.loading').length");
    }

    /**
     * @When I press button :arg1
     */
    public function iPressButton($arg1)
    {
        $button = $this->getSession()->getPage()->find('xpath', '//div[text()="'.$arg1.'"]');
        $this->button_id = $button->getAttribute('id');
        $button->click();
    }

    /**
     * @Given I click link :arg1
     */
    public function iClickLink($arg1)
    {
        $link = $this->getSession()->getPage()->find('xpath', '//a[text()="'.$arg1.'"]');
        $link->click();
    }

    /**
     * @Then I see button :arg1
     */
    public function iSee($arg1)
    {
        $element = $this->getSession()->getPage()->find('xpath', '//div[text()="'.$arg1.'"]');
        if ($element->getAttribute('style')) {
            throw new \Exception("Element with text \"$arg1\" must be invisible");
        }
    }

    /**
     * @Then The :field field should start with :value
     */
    public function fieldShouldContain($field, $value)
    {
        $field = $this->assertSession()->fieldExists($field);

        if(0 !== strpos($field->getValue(), $value)) {
            throw new \Exception('Field value '.$field->getValue().' does not start with '.$value);
        }

    }

    /**
     * @Then dump :arg1
     */
    public function dump($arg1)
    {
        $element = $this->getSession()->getPage()->find('xpath', '//div[text()="'.$arg1.'"]');
        var_dump($element->getOuterHtml());
    }

    /**
     * @Then I don't see button :arg1
     */
    public function iDontSee($arg1)
    {
        $element = $this->getSession()->getPage()->find('xpath', '//div[text()="'.$arg1.'"]');
        if (strpos('display: none', $element->getAttribute('style')) !== false) {
            throw new \Exception("Element with text \"$arg1\" must be invisible");
        }
    }

    /**
     * @Then Label changes to a number
     */
    public function labelChangesToANumber()
    {
        $element = $this->getSession()->getPage()->findById($this->button_id);
        if (!is_numeric($element->getHtml())) {
            throw new \Exception('Label must be numeric');
        }
    }

    /**
     * @Then Modal opens with text :arg1
     */
    public function modalOpensWithText($arg1)
    {
        $modal = $this->getSession()->getPage()->find('xpath', '//div[text()="'.$arg1.'"]');
        if ($modal->getAttribute('class') != 'ui modal visible active') {
            throw new \Exception('No such modal');
        }
    }

    /**
     * @Then Progress bar should be go all the way
     */
    public function progressBarShouldBeGoAllTheWay()
    {
        /*$element =*/ $this->getSession()->getPage()->find('css', '.bar');
        //TODO: zombiejs does not support sse :(
        //var_dump($element->getOuterHtml());
    }
}
