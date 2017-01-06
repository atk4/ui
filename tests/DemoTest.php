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
    }

    public function testFields()
    {
        $this->expectOutputRegex($this->regex);
        include 'field.php';
    }
}
