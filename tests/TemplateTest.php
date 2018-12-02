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

    /**
     * Test isTopTag().
     */
    public function testIsTopTag()
    {
        $t = new \atk4\ui\Template('a{$foo}b');
        $this->assertEquals(true, $t->isTopTag('_top'));
        $this->assertEquals(false, $t->isTopTag('foo'));
    }

    /**
     * Test getTagRef().
     */
    public function testGetTagRef()
    {
        // top tag
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $t1 =& $t->getTagRef('_top');
        $this->assertEquals(['','foo#1'=>['hello'],', cruel ','bar#1'=>['world'],'. ','foo#2'=>['hello']], $t1);

        $t1 = ['good bye']; // will change $t->template because it's by reference
        $this->assertEquals(['good bye'], $t->template);

        // any tag
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $t2 =& $t->getTagRef('foo');
        $this->assertEquals(['hello'], $t2);

        $t2 = ['good bye']; // will change $t->template because it's by reference
        $this->assertEquals(['','foo#1'=>['good bye'],', cruel ','bar#1'=>['world'],'. ','foo#2'=>['hello']], $t->template);
    }

    /**
     * Test getTagRefList().
     */
    public function testGetTagRefList()
    {
        /*
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $template = $t->template;

        $t->getTagRefList('_top', $template);
        var_dump($template);
        */
    }
}
