<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Atk4\Core\WarnDynamicPropertyTrait;
use Behat\Mink\Driver\Selenium2Driver;

class MinkSession extends \Behat\Mink\Session
{
    use WarnDynamicPropertyTrait;

    public function getDriver(): Selenium2Driver
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
}
