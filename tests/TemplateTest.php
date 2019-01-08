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
     * Exception in getTagRef().
     *
     * @expectedException Exception
     */
    public function testGetTagRefException()
    {
        $t = new \atk4\ui\Template('{foo}hello{/}');
        $t->getTagRef('bar'); // not existent tag
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
     * Non existant template - throw exception.
     *
     * @expectedException Exception
     */
    public function testBadTemplate1()
    {
        $t = new \atk4\ui\Template();
        $t->load('bad_template_file');
    }

    /**
     * Non existant template - no exception.
     */
    public function testBadTemplate2()
    {
        $t = new \atk4\ui\Template();
        $this->assertFalse($t->tryLoad('bad_template_file'));
    }

    /**
     * Exception in getTagRefList().
     *
     * @expectedException Exception
     */
    public function testGetTagRefListException()
    {
        $t = new \atk4\ui\Template('{foo}hello{/}');
        $t->getTagRefList('bar'); // not existent tag
    }

    /**
     * Test hasTag().
     */
    public function testHasTag()
    {
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $this->assertTrue($t->hasTag(['foo', 'bar'])); // all tags exist
        $this->assertFalse($t->hasTag(['foo', 'bar', 'qwe'])); // qwe tag does not exist
    }

    /**
     * Test set() exception.
     *
     * @expectedException Exception
     */
    public function testSetException1()
    {
        $t = new \atk4\ui\Template('{foo}hello{/} guys');
        $t->set('qwe', 'Hello'); // not existent tag
    }

    /**
     * Test set() exception.
     *
     * @expectedException Exception
     */
    public function testSetException2()
    {
        $t = new \atk4\ui\Template('{foo}hello{/} guys');
        $t->set('foo', new \StdClass()); // bad value
    }

    /**
     * Test set, append, tryAppend, tryAppendHTML, del, tryDel.
     */
    public function testSetAppendDel()
    {
        $t = new \atk4\ui\Template('{foo}hello{/} guys');

        // del tests
        $t->set('foo', 'Hello');
        $t->del('foo');
        $this->assertEquals(' guys', $t->render());
        $t->set('foo', 'Hello');
        $t->tryDel('qwe'); // non existent tag, ignores
        $this->assertEquals('Hello guys', $t->render());

        // set and append tests
        $t->set('foo', 'Hello');
        $t->set('foo', 'Hi'); // overwrites
        $t->setHTML('foo', '<b>Hi</b>'); // overwrites
        $t->trySet('qwe', 'ignore this'); // ignores
        $t->trySetHTML('qwe', '<b>ignore</b> this'); // ignores

        $t->append('foo', ' and'); // appends
        $t->appendHTML('foo', ' <b>welcome</b> my'); // appends
        $t->tryAppend('foo', ' dear'); // appends
        $t->tryAppend('qwe', 'ignore this'); // ignores
        $t->tryAppendHTML('foo', ' and <b>smart</b>'); // appends html
        $t->tryAppendHTML('qwe', '<b>ignore</b> this'); // ignores

        $this->assertEquals('<b>Hi</b> and <b>welcome</b> my dear and <b>smart</b> guys', $t->render());
    }

    /**
     * ArrayAccess test.
     */
    public function testArrayAccess()
    {
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}welcome{/}');

        $this->assertTrue(isset($t['foo']));

        $t['foo'] = 'Hi';
        $this->assertEquals(['Hi'], $t['foo']);

        unset($t['foo']);
        $this->assertEquals([], $t['foo']);

        $this->assertTrue(isset($t['foo'])); // it's still set even after unset - that's specific for Template
    }

    /**
     * Test eachTag.
     */
    public function testEachTag()
    {
        $t = new \atk4\ui\Template('{foo}hello{/}, {how}cruel{/how} {bar}world{/}. {foo}welcome{/}');

        // don't throw exception if tag does not exist
        $t->eachTag('ignore', function () {
        });

        // replace values in these tags
        $t->eachTag(['foo', 'bar'], function ($value, $tag) {
            return strtoupper($value);
        });
        $this->assertEquals('HELLO, cruel WORLD. WELCOME', $t->render());

        // tag contains all template (for example in Lister)
        $t = new \atk4\ui\Template('{foo}hello{/}');
        $t->eachTag('foo', function ($value, $tag) {
            return strtoupper($value);
        });
        $this->assertEquals('HELLO', $t->render());
    }

    /**
     * Clone region.
     */
    public function testClone()
    {
        $t = new \atk4\ui\Template('{foo}hello{/} guys');

        // clone only {foo} region
        $t1 = $t->cloneRegion('foo');
        $this->assertEquals('hello', $t1->render());

        // clone all template
        $t1 = $t->cloneRegion('_top');
        $this->assertEquals('hello guys', $t1->render());
    }

    /**
     * Try to load template from non existent file - exception.
     *
     * @expectedException Exception
     */
    public function testLoadException()
    {
        $t = new \atk4\ui\Template();
        $t->load('such-file-does-not-exist.txt');
    }

    /**
     * Test renderRegion.
     */
    public function testRenderRegion()
    {
        $t = new \atk4\ui\Template('{foo}hello{/} guys');
        $this->assertEquals('hello', $t->render('foo'));
    }

    public function testDollarTags()
    {
        $t = new \atk4\ui\Template('{$foo} guys and {$bar} here');
        $t->set([
            'foo' => 'Hello',
            'bar' => 'welcome',
        ]);
        $this->assertEquals('Hello guys and welcome here', $t->render());
    }
}
