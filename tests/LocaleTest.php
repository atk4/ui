<?php

namespace atk4\ui\tests;

use atk4\ui\Exception;
use atk4\ui\Locale;

class LocaleTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $this->expectException(Exception::class);
        $exc = new Locale();
    }

    public function testGetPath()
    {
        $path = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'src', '..', 'locale']) . DIRECTORY_SEPARATOR;
        $this->assertEquals($path, Locale::getPath());
    }
}
