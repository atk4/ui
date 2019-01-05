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
        $t1 = &$t->getTagRef('_top');
        $this->assertEquals(['', 'foo#1'=>['hello'], ', cruel ', 'bar#1'=>['world'], '. ', 'foo#2'=>['hello']], $t1);

        $t1 = ['good bye']; // will change $t->template because it's by reference
        $this->assertEquals(['good bye'], $t->template);

        // any tag
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $t2 = &$t->getTagRef('foo');
        $this->assertEquals(['hello'], $t2);

        $t2 = ['good bye']; // will change $t->template because it's by reference
        $this->assertEquals(['', 'foo#1'=>['good bye'], ', cruel ', 'bar#1'=>['world'], '. ', 'foo#2'=>['hello']], $t->template);
    }

    /**
     * Test getTagRefList().
     */
    public function testGetTagRefList()
    {
        // top tag
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $t1 = $t->getTagRefList('_top');
        $this->assertEquals([['', 'foo#1'=>['hello'], ', cruel ', 'bar#1'=>['world'], '. ', 'foo#2'=>['hello']]], $t1);

        $t1[0] = ['good bye']; // will change $t->template because it's by reference
        $this->assertEquals(['good bye'], $t->template);

        // any tag
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $t2 = $t->getTagRefList('foo');
        $this->assertEquals([['hello'], ['hello']], $t2);

        $t2[1] = ['good bye']; // will change $t->template last "foo" tag because it's by reference
        $this->assertEquals(['', 'foo#1'=>['hello'], ', cruel ', 'bar#1'=>['world'], '. ', 'foo#2'=>['good bye']], $t->template);

        // array of tags
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $t2 = $t->getTagRefList(['foo', 'bar']);
        $this->assertEquals([['hello'], ['hello'], ['world']], $t2);

        $t2[1] = ['good bye']; // will change $t->template last "foo" tag because it's by reference
        $t2[2] = ['planet'];   // will change $t->template "bar" tag because it's by reference too
        $this->assertEquals(['', 'foo#1'=>['hello'], ', cruel ', 'bar#1'=>['planet'], '. ', 'foo#2'=>['good bye']], $t->template);
    }
    
    /**
     * Non existant template - throw exception
     *
     * @expectedException Exception
     */
     public function testBadTemplate1()
     {
        $t = new \atk4\ui\Template();
        $t->load('bad_template_file');
     }

    /**
     * Non existant template - no exception
     */
     public function testBadTemplate2()
     {
        $t = new \atk4\ui\Template();
        $this->assertFalse($t->tryLoad('bad_template_file'));
     }
}
