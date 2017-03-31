<?php

namespace atk4\ui\tests;

/**
 * Making sure demo pages don't throw exceptions and coverage is
 * handled.
 */
class DemoTest extends \atk4\core\PHPUnit_AgileTestCase
{
    public function setUp()
    {
        chdir('demos');
    }

    public function tearDown()
    {
        chdir('..');
    }

    private $regex = '/^..DOCTYPE/';

    public function testButtons()
    {
        $this->expectOutputRegex($this->regex);
        include 'button.php';
        $app->run();
    }

    public function testFields()
    {
        $this->expectOutputRegex($this->regex);
        include 'field.php';
        $app->run();
    }

    public function testLayout()
    {
        $this->expectOutputRegex($this->regex);
        include 'layout.php';
    }

    public function testTable()
    {
        $this->expectOutputRegex($this->regex);
        include 'table.php';
        $app->run();
    }

    public function testPaginator()
    {
        $this->expectOutputRegex($this->regex);
        include 'paginator.php';
        $app->run();
    }
}
