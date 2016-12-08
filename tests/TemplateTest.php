<?php

namespace atk4\ui\tests;

class TemplateTest extends \atk4\core\PHPUnit_AgileTestCase
{
    /**
     * Test constructor.
     */
    public function testBasicInit()
    {
        $t = new \atk4\ui\Template('hello, {foo}world{/}');
        $t['foo'] = 'bar';

        $this->assertEquals('hello, bar', $t->render());
    }

}
