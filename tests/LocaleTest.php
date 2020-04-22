<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\ui\Exception;
use atk4\ui\Locale;

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
        $this->assertEquals($rootDir . DIRECTORY_SEPARATOR . 'locale', realpath(Locale::getPath()));
    }
}
