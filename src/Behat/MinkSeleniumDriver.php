<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Atk4\Core\WarnDynamicPropertyTrait;
use WebDriver\Element as WebDriverElement;

class MinkSeleniumDriver extends \Behat\Mink\Driver\Selenium2Driver
{
    use WarnDynamicPropertyTrait;

    public function __construct(\Behat\Mink\Driver\Selenium2Driver $driver) // @phpstan-ignore-line
    {
        $class = self::class;
        while (($class = get_parent_class($class)) !== false) {
            \Closure::bind(function () use ($driver) {
                foreach (get_object_vars($driver) as $k => $v) {
                    $this->{$k} = $v;
                }
            }, $this, $class)();
        }
    }

    public function getText($xpath): string
    {
        // HTMLElement::innerText returns rendered text as when copied to the clipboard
        // https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/innerText
        // https://github.com/minkphp/MinkSelenium2Driver/pull/327
        // https://github.com/minkphp/MinkSelenium2Driver/pull/328
        return $this->executeJsOnXpath($xpath, 'return {{ELEMENT}}.innerText;');
    }

    protected function findElement(string $xpath): WebDriverElement
    {
        return \Closure::bind(function () use ($xpath) {
            return $this->findElement($xpath);
        }, $this, parent::class)();
    }

    protected function clickOnElement(WebDriverElement $element): void
    {
        \Closure::bind(function () use ($element) {
            $this->clickOnElement($element);
        }, $this, parent::class)();
    }

    protected function mouseOverElement(WebDriverElement $element): void
    {
        // move the element into the viewport
        // needed at least for Firefox as Selenium moveto does move the mouse cursor only
        $this->executeScript('arguments[0].scrollIntoView({ behaviour: \'instant\', block: \'center\', inline: \'center\' })', [$element]);

        $this->getWebDriverSession()->moveto(['element' => $element->getID()]);
    }

    private function executeJsSelectText(WebDriverElement $element, int $start, int $stop = null): void
    {
        $this->executeScript(
            'arguments[0].setSelectionRange(Math.min(arguments[1], Number.MAX_SAFE_INTEGER), Math.min(arguments[2], Number.MAX_SAFE_INTEGER));',
            [$element, $start, $stop ?? $start]
        );
    }

    /**
     * @param 'type' $action
     * @param string $options
     */
    protected function executeSynJsAndWait(string $action, WebDriverElement $element, $options): void
    {
        $this->withSyn();

        $waitUniqueKey = '__wait__' . hash('sha256', microtime(true) . random_bytes(64));
        $this->executeScript(
            'window.syn[arguments[2]] = true; window.syn.' . $action . '(arguments[0], arguments[1], () => delete window.syn[arguments[2]]);',
            [$element, $options, $waitUniqueKey]
        );
        $this->wait(5000, 'typeof window.syn[arguments[0]] === \'undefined\'', [$waitUniqueKey]);
    }

    /**
     * @param string $text special characters can be passed like "[shift]T[shift-up]eest[left][left][backspace]"
     */
    public function keyboardWrite(string $xpath, $text): void
    {
        $element = $this->findElement($xpath);

        $focusedElement = $this->getWebDriverSession()->activeElement();
        if ($element->getID() !== $focusedElement->getID()) {
            $this->clickOnElement($element);
            $focusedElement = $this->getWebDriverSession()->activeElement();
        }

        if (in_array($focusedElement->name(), ['input', 'textarea'], true)) {
            $this->executeJsSelectText($focusedElement, \PHP_INT_MAX);
        }

        $this->executeSynJsAndWait('type', $element, $text);
    }
}
