<?php
namespace atk4\ui\tests;

class ViewTest extends \atk4\core\PHPUnit_AgileTestCase
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
}
