<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;

class ViewTest extends AtkPhpunit\TestCase
{
    /**
     * Test redering multiple times.
     */
    public function testMultipleRender()
    {
        $v = new \atk4\ui\View();
        $v->set('foo');

        $a = $v->render();
        $b = $v->render();
        $this->assertEquals($a, $b);
    }

    /**
     * @expectedException Exception
     */
    public function testAddAfterRender()
    {
        $v = new \atk4\ui\View();
        $v->set('foo');

        $a = $v->render();
        \atk4\ui\View::addTo($v);  // this should fail. No adding after rendering.
        $b = $v->render();
        $this->assertEquals($a, $b);
    }
}
