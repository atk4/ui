<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Ui\Repro\Repro;
use PHPUnit\Framework\TestCase;

class JsTest extends TestCase
{
    public function testNumbers(): void
    {
        Repro::repro();

        self::assertTrue(true);
    }
}
