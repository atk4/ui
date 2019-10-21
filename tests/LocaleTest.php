<?php
namespace atk4\data\tests;
use atk4\data\Exception;
use atk4\data\Locale;
class LocaleTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $this->expectException(Exception::class);
        $exc = new Locale();
    }
    public function testGetPath()
    {
        $path = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'src', '..', 'locale']).DIRECTORY_SEPARATOR;
        $this->assertEquals($path, Locale::getPath());
    }
}
