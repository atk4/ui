<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use PHPUnit\Framework\TestCase;

class JsTest extends TestCase
{
    public function testNumbers(): void
    {
        if (\PHP_INT_SIZE === 4) {
            self::markTestIncomplete('Test is not supported on 32bit php');
        }

        $v = 'x';
    }
}
