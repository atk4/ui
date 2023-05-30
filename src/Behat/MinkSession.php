<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Atk4\Core\WarnDynamicPropertyTrait;
use Behat\Mink\Element\NodeElement;

class MinkSession extends \Behat\Mink\Session
{
    use WarnDynamicPropertyTrait;

    public function __construct(\Behat\Mink\Session $session)
    {
        $driver = new MinkSeleniumDriver($session->getDriver()); // @phpstan-ignore-line

        parent::__construct($driver, $session->getSelectorsHandler());
    }

    public function getDriver(): MinkSeleniumDriver
    {
        return parent::getDriver(); // @phpstan-ignore-line
    }

    public function executeScript($script, array $args = []): void
    {
        $this->getDriver()->executeScript($script, $args);
    }

    public function evaluateScript($script, array $args = [])
    {
        return $this->getDriver()->evaluateScript($script, $args);
    }

    public function wait($time, $condition = 'false', array $args = [])
    {
        return $this->getDriver()->wait($time, $condition, $args);
    }

    public function keyboardWrite(NodeElement $element, string $text): void
    {
        $this->getDriver()->keyboardWrite($element->getXpath(), $text);
    }
}
