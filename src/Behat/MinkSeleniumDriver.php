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

    protected function mouseOverElement(WebDriverElement $element): void
    {
        // move the element into the viewport
        // needed at least for Firefox as Selenium moveto does move the mouse cursor only
        $this->executeScript('arguments[0].scrollIntoView({ behaviour: \'instant\', block: \'center\', inline: \'center\' })', [$element]);

        $this->getWebDriverSession()->moveto(['element' => $element->getID()]);
    }
}
