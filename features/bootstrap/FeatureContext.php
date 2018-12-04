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

        if (0 !== strpos($field->getValue(), $value)) {
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

    /**
     * @Then I select :arg1 in dropdown :arg2
     *
     * Finds a dropdown, opens it, waits, selects specified value and waits for it to close
     */
    public function iSelectInDropdown($select_option, $css_selector)
    {

        // TODO: not sure if initial wait is needed
        $dropdown_arrow = $this->webDriver->wait(5)->until(
            \WebDriverExpectedCondition::elementToBeClickable(\WebDriverBy::cssSelector($css_selector.' i.dropdown.icon'))
        );
        usleep(100000);

        // expand the menu
        $dropdown_arrow->click();

        // wait until options are visible
        $this->webDriver->wait(5, 200)->until(
            \WebDriverExpectedCondition::visibilityOfElementLocated(\WebDriverBy::cssSelector($css_selector.' div.menu.visible'))
        );

        //store current value for later rollback
        // TODO: probably not needed for our tests
        $this->dropDownInitiallySelected = $this->webDriver->findElement(\WebDriverBy::cssSelector($css_selector.' div.menu div.selected'))->getAttribute('data-value');

        //select a non-selected element
        $dropdown_options = $this->webDriver->findElements(\WebDriverBy::cssSelector($css_selector.' div.menu div'));
        $option_selected = false;

        //if no option to select was specified, select some which is not
        //empty and not selected yet
        if ($select_option === null) {
            foreach ($dropdown_options as $option) {
                //do not select the show all option (....)
                if ($option->getAttribute('data-value') == '') {
                    continue;
                }
                //do not select the option already active
                if (strpos($option->getAttribute('class'), 'selected') !== false) {
                    continue;
                }
                $option_selected = $option->getAttribute('data-value');
                $option->click();
                break;
            }
        } else {
            //select specific value
            $option = $this->webDriver->findElement(\WebDriverBy::cssSelector($css_selector.' div.menu div[data-value="'.$select_option.'"]'));
            $option_selected = $option->getAttribute('data-value');
            $option->click();

            // TODO - need to assert that option exists here
        }

        // wait for dropdown menu to disappear
        if ($wait_menu_disappear) {
            $this->waitUntilInvisible($css_selector.' div.menu.visible');
        }
    }
}
