<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Atk4\Core\WarnDynamicPropertyTrait;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session as BaseSession;

class MinkSession extends BaseSession
{
    use WarnDynamicPropertyTrait;

    public function __construct(BaseSession $session)
    {
        $driver = new MinkSeleniumDriver($session->getDriver()); // @phpstan-ignore argument.type

        parent::__construct($driver, $session->getSelectorsHandler()); // @phpstan-ignore method.deprecated
    }

    #[\Override]
    public function getDriver(): MinkSeleniumDriver
    {
        return parent::getDriver(); // @phpstan-ignore return.type
    }

    #[\Override]
    public function executeScript($script, array $args = []): void
    {
        $this->getDriver()->executeScript($script, $args);
    }

    #[\Override]
    public function evaluateScript($script, array $args = [])
    {
        return $this->getDriver()->evaluateScript($script, $args);
    }

    #[\Override]
    public function wait($time, $condition = 'false', array $args = [])
    {
        return $this->getDriver()->wait($time, $condition, $args);
    }

    public function keyboardWrite(NodeElement $element, string $text): void
    {
        $this->getDriver()->keyboardWrite($element->getXpath(), $text);
    }
}
