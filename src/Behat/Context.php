<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\RawMinkContext;
use Exception;

class Context extends RawMinkContext implements BehatContext
{
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
        $this->disableDebounce();
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
                throw new Exception('Page contains uncaught exception');
            }
        }
    }

    protected function disableDebounce(): void
    {
        $this->getSession()->executeScript('atk.options.set("debounceTimeout", 20)');
    }

    /**
     * Sleep for a certain time in ms.
     *
     * @Then I sleep :arg1 ms
     */
    public function iSleep(int $arg1): void
    {
        $this->getSession()->wait($arg1);
    }

    /**
     * @When I press button :arg1
     */
    public function iPressButton(string $arg1): void
    {
        $button = $this->getSession()->getPage()->find('xpath', '//div[text()="' . $arg1 . '"]');
        // store button id.
        $this->buttonId = $button->getAttribute('id');
        // fix "is out of bounds of viewport width and height" for Firefox
        $button->focus();
        $button->click();
    }

    /**
     * @Then I press menu button :arg1 using class :arg2
     */
    public function iPressMenuButtonUsingClass(string $arg1, string $arg2): void
    {
        $menu = $this->getSession()->getPage()->find('css', '.ui.menu.' . $arg2);
        if (!$menu) {
            throw new Exception('Unable to find a menu with class ' . $arg2);
        }

        $link = $menu->find('xpath', '//a[text()="' . $arg1 . '"]');
        if (!$link) {
            throw new Exception('Unable to find menu with title ' . $arg1);
        }

        $this->getSession()->executeScript('$("#' . $link->getAttribute('id') . '").click()');
    }

    /**
     * @Then I set calendar input name :arg1 with value :arg2
     */
    public function iSetCalendarInputNameWithValue(string $arg1, string $arg2): void
    {
        $script = '$(\'input[name="' . $arg1 . '"]\').get(0)._flatpickr.setDate("' . $arg2 . '")';
        $this->getSession()->executeScript($script);
    }

    /**
     * @Given I click link :arg1
     */
    public function iClickLink(string $arg1): void
    {
        $link = $this->getSession()->getPage()->find('xpath', '//a[text()="' . $arg1 . '"]');
        $link->click();
    }

    /**
     * @Then I click filter column name :arg1
     */
    public function iClickFilterColumnName(string $arg1): void
    {
        $column = $this->getSession()->getPage()->find('css', "th[data-column='" . $arg1 . "']");
        if (!$column) {
            throw new Exception('Unable to find a column ' . $arg1);
        }

        $icon = $column->find('css', 'i');
        if (!$icon) {
            throw new Exception('Column does not contain clickable icon.');
        }

        $this->getSession()->executeScript('$("#' . $icon->getAttribute('id') . '").click()');
    }

    /**
     * @Given I click tab with title :arg1
     */
    public function iClickTabWithTitle(string $arg1): void
    {
        $tabMenu = $this->getSession()->getPage()->find('css', '.ui.tabular.menu');
        if (!$tabMenu) {
            throw new Exception('Unable to find a tab menu.');
        }

        $link = $tabMenu->find('xpath', '//a[text()="' . $arg1 . '"]');
        if (!$link) {
            throw new Exception('Unable to find tab with title ' . $arg1);
        }

        $this->getSession()->executeScript('$("#' . $link->getAttribute('id') . '").click()');
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
    public function iClickFirstElementUsingClass(string $arg1): void
    {
        $this->getSession()->executeScript('$("' . $arg1 . '")[0].click()');
    }

    /**
     * @Then I click paginator page :arg1
     */
    public function iClickPaginatorPage(string $arg1): void
    {
        $this->getSession()->executeScript('$("a.item[data-page=' . $arg1 . ']").click()');
    }

    /**
     * @Then I see button :arg1
     */
    public function iSee(string $arg1): void
    {
        $element = $this->getSession()->getPage()->find('xpath', '//div[text()="' . $arg1 . '"]');
        if ($element->getAttribute('style')) {
            throw new Exception('Element with text "' . $arg1 . '" must be invisible');
        }
    }

    /**
     * @Then dump :arg1
     */
    public function dump(string $arg1): void
    {
        $element = $this->getSession()->getPage()->find('xpath', '//div[text()="' . $arg1 . '"]');
        var_dump($element->getOuterHtml());
    }

    /**
     * @Then I don't see button :arg1
     */
    public function iDontSee(string $arg1): void
    {
        $element = $this->getSession()->getPage()->find('xpath', '//div[text()="' . $arg1 . '"]');
        if (mb_strpos('display: none', $element->getAttribute('style')) !== false) {
            throw new Exception('Element with text "' . $arg1 . '" must be invisible');
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
     * @Then I press Modal button :arg
     */
    public function iPressModalButton(string $arg): void
    {
        $modal = $this->getSession()->getPage()->find('css', '.modal.transition.visible.active.front');
        if ($modal === null) {
            throw new Exception('No modal found');
        }
        // find button in modal
        $btn = $modal->find('xpath', '//div[text()="' . $arg . '"]');
        if (!$btn) {
            throw new Exception('Cannot find button in modal');
        }
        $btn->click();
    }

    /**
     * @Then Modal is open with text :arg1
     *
     * Check if text is present in modal or dynamic modal.
     */
    public function modalIsOpenWithText(string $arg1): void
    {
        $modal = $this->waitForNodeElement('.modal.transition.visible.active.front');
        if ($modal === null) {
            throw new Exception('No modal found');
        }
        // find text in modal
        $text = $modal->find('xpath', '//div[text()="' . $arg1 . '"]');
        if (!$text || trim($text->getText()) !== $arg1) {
            throw new Exception('No such text in modal');
        }
    }

    /**
     * @Then Modal is showing text :arg1 inside tag :arg2
     */
    public function modalIsShowingText(string $arg1, string $arg2): void
    {
        // get modal
        $modal = $this->waitForNodeElement('.modal.transition.visible.active.front');
        if ($modal === null) {
            throw new Exception('No modal found');
        }
        // find text in modal
        $text = $modal->find('xpath', '//' . $arg2 . '[text()="' . $arg1 . '"]');
        if (!$text || $text->getText() !== $arg1) {
            throw new Exception('No such text in modal');
        }
    }

    /**
     * @Then Active tab should be :arg1
     */
    public function activeTabShouldBe(string $arg1): void
    {
        $tab = $this->getSession()->getPage()->find('css', '.ui.tabular.menu > .item.active');
        if ($tab->getText() !== $arg1) {
            throw new Exception('Active tab is not ' . $arg1);
        }
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
    public function toastDisplayShouldContainText(string $arg1): void
    {
        // get toast
        $toast = $this->getSession()->getPage()->find('css', '.ui.toast-container');
        if ($toast === null) {
            throw new Exception('No toast found');
        }
        $content = $toast->find('css', '.content');
        if ($content === null) {
            throw new Exception('No Content in Toast');
        }
        // find text in toast
        $text = $content->find('xpath', '//div');
        if (!$text || mb_strpos($text->getText(), $arg1) === false) {
            throw new Exception('No such text in toast');
        }
    }

    /**
     * @Then I select value :arg1 in lookup :arg2
     *
     * Select a value in a lookup control.
     */
    public function iSelectValueInLookup(string $arg1, string $arg2): void
    {
        // get dropdown item from semantic ui which is direct parent of input html element
        $inputElem = $this->getSession()->getPage()->find('css', 'input[name=' . $arg2 . ']');
        if ($inputElem === null) {
            throw new Exception('Lookup element not found: ' . $arg2);
        }
        $lookupElem = $inputElem->getParent();

        // open dropdown and wait till fully opened (just a click is not triggering it)
        $this->getSession()->executeScript('$("#' . $lookupElem->getAttribute('id') . '").dropdown("show")');
        $this->jqueryWait('$("#' . $lookupElem->getAttribute('id') . '").hasClass("visible")');

        // select value
        $valueElem = $lookupElem->find('xpath', '//div[text()="' . $arg1 . '"]');
        if ($valueElem === null || $valueElem->getText() !== $arg1) {
            throw new Exception('Value not found: ' . $arg1);
        }
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
     * @Then I search grid for :arg1
     */
    public function iSearchGridFor(string $arg1): void
    {
        $search = $this->getSession()->getPage()->find('css', 'input.atk-grid-search');
        if (!$search) {
            throw new Exception('Unable to find search input.');
        }

        $search->setValue($arg1);
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
     * @Then /^I wait for the page to be loaded$/
     */
    public function waitForThePageToBeLoaded(): void
    {
        // This line in test-unit.yml is causing test to fail. Need to increase wait time to compensate.
        // sed -i 's/usleep(100000)/usleep(5000)/' vendor/behat/mink-selenium2-driver/src/Selenium2Driver.php
        usleep(500000);
        $this->getSession()->wait(10000, "document.readyState === 'complete'");
    }

    /**
     * @Then I click icon using css :arg1
     */
    public function iClickIconUsingCss(string $arg1): void
    {
        $icon = $this->getSession()->getPage()->find('css', $arg1);
        if (!$icon) {
            throw new Exception('Unable to find search remove icon.');
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
    public function iWaitForLoadingToStartIn(string $arg1): void
    {
        $this->getSession()->wait(2000, '$("' . $arg1 . '").hasClass("loading")');
    }

    protected function getFinishedScript(): string
    {
        return 'document.readyState === \'complete\''
            . ' && typeof jQuery !== \'undefined\' && jQuery.active === 0'
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

        throw new Exception('jQuery did not finished within a time limit');
    }

    /**
     * @Then /^the field "([^"]*)"  should start with "([^"]*)"$/
     */
    public function theFieldShouldStartWith(string $arg1, string $arg2): void
    {
        $field = $this->assertSession()->fieldExists($arg1);

        if (mb_strpos($field->getValue(), $arg2) === false) {
            throw new Exception('Field value ' . $field->getValue() . ' does not start with ' . $arg2);
        }
    }
}
