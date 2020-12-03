<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;
use Atk4\Ui\Exception;
use Atk4\Ui\Locale;

class LocaleTest extends AtkPhpunit\TestCase
{
    public function testException()
    {
        $this->expectException(Exception::class);
        $exc = new Locale();
    }

    public function testGetPath()
    {
        $rootDir = realpath(dirname(__DIR__) . '/src/..');
        $this->assertSame($rootDir . \DIRECTORY_SEPARATOR . 'locale', realpath(dirname(Locale::getPath())) . \DIRECTORY_SEPARATOR . basename(Locale::getPath()));
    }
}
