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

    public function inc($f)
    {
        $_SERVER['REQUEST_URI'] = '/ui/'.$f;
        include $f;

        return $app;
    }

    private $regex = '/^..DOCTYPE/';

    /**
     * @dataProvider demoList
     */
    public function testDemo($page)
    {
        $this->expectOutputRegex($this->regex);
        $this->inc($page)->run();
    }



    public function demoList()
    {
        return [
            ['button.php'], 
            ['table.php'],
            ['form.php'],
            ['form2.php'],
            ['multitable.php'],
            ['grid.php'],
            ['crud.php'],

            ['view.php'],
            ['field.php'],
            ['message.php'],
            ['header.php'],
            ['label.php'],
            ['menu.php'],
            ['tabs.php'],
            ['paginator.php'],

            ['button2.php'],
            ['reloading.php'],
            ['modal.php'],
            ['sticky.php'],
            ['recursive.php'],
        ];
    }

 public function testLayout()
 {
     $this->expectOutputRegex($this->regex);
     include 'layouts_manual.php';
 }


}
