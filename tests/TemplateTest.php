<?php

declare(strict_types=1);

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\ui\Exception;

class TemplateTest extends AtkPhpunit\TestCase
{
    /**
     * Test constructor.
     */
    public function testBasicInit()
    {
        $t = new \atk4\ui\Template('hello, {foo}world{/}');
        $t['foo'] = 'bar';

        $this->assertSame('hello, bar', $t->render());
    }

    /**
     * Test getTagRef().
     */
    public function testGetTagRef()
    {
        // top tag
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $t1 = &$this->callProtected($t, 'getTagRef', '_top');
        $this->assertSame(['foo#0' => ['hello'], ', cruel ', 'bar#0' => ['world'], '. ', 'foo#1' => ['hello']], $t1);

        $t1 = ['good bye']; // will change $t->template because it's by reference
        $this->assertSame(['good bye'], $this->getProtected($t, 'template'));

        // any tag
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $t2 = &$this->callProtected($t, 'getTagRef', 'foo');
        $this->assertSame(['hello'], $t2);

        $t2 = ['good bye']; // will change $t->template because it's by reference
        $this->assertSame(['foo#0' => ['good bye'], ', cruel ', 'bar#0' => ['world'], '. ', 'foo#1' => ['hello']], $this->getProtected($t, 'template'));
    }

    /**
     * Exception in getTagRef().
     */
    public function testGetTagRefException()
    {
        $t = new \atk4\ui\Template('{foo}hello{/}');
        $this->expectException(Exception::class);
        $this->callProtected($t, 'getTagRef', 'bar'); // not existent tag
    }

    /**
     * Test getTagRefs().
     */
    public function testGetTagRefs()
    {
        // top tag
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello2{/}');
        $t1 = $this->callProtected($t, 'getTagRefs', '_top');
        $this->assertSame([['foo#0' => ['hello'], ', cruel ', 'bar#0' => ['world'], '. ', 'foo#1' => ['hello2']]], $t1);

        $t1[0] = ['good bye']; // will change $t->template because it's by reference
        $this->assertSame(['good bye'], $this->getProtected($t, 'template'));

        // any tag
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello2{/}');
        $t2 = $this->callProtected($t, 'getTagRefs', 'foo');
        $this->assertSame([['hello'], ['hello2']], $t2);
        $t2[1] = ['good bye']; // will change $t->template last "foo" tag because it's by reference
        $this->assertSame(['foo#0' => ['hello'], ', cruel ', 'bar#0' => ['world'], '. ', 'foo#1' => ['good bye']], $this->getProtected($t, 'template'));

        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}hello2{/}');
        $t2 = $this->callProtected($t, 'getTagRefs', 'bar');
        $this->assertSame([['world']], $t2);
        $t2[0] = ['planet']; // will change $t->template last "foo" tag because it's by reference
        $this->assertSame(['foo#0' => ['hello'], ', cruel ', 'bar#0' => ['planet'], '. ', 'foo#1' => ['hello2']], $this->getProtected($t, 'template'));
    }

    /**
     * Non existent template - throw exception.
     */
    public function testBadTemplate1()
    {
        $t = new \atk4\ui\Template();
        $this->expectException(Exception::class);
        $t->load('bad_template_file');
    }

    /**
     * Non existent template - no exception.
     */
    public function testBadTemplate2()
    {
        $t = new \atk4\ui\Template();
        $this->assertFalse($t->tryLoad('bad_template_file'));
    }

    /**
     * Exception in getTagRefs().
     */
    public function testGetTagRefsException()
    {
        $t = new \atk4\ui\Template('{foo}hello{/}');
        $this->expectException(Exception::class);
        $this->callProtected($t, 'getTagRefs', 'bar'); // not existent tag
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
     */
    public function testSetException1()
    {
        $t = new \atk4\ui\Template('{foo}hello{/} guys');
        $this->expectException(Exception::class);
        $t->set('qwe', 'Hello'); // not existent tag
    }

    /**
     * Test set() exception.
     */
    public function testSetException2()
    {
        $t = new \atk4\ui\Template('{foo}hello{/} guys');
        $this->expectException(Exception::class);
        $t->set('foo', new \StdClass()); // bad value
    }

    /**
     * Test set, append, tryAppend, tryAppendHtml, del, tryDel.
     */
    public function testSetAppendDel()
    {
        $t = new \atk4\ui\Template('{foo}hello{/} guys');

        // del tests
        $t->set('foo', 'Hello');
        $t->del('foo');
        $this->assertSame(' guys', $t->render());
        $t->set('foo', 'Hello');
        $t->tryDel('qwe'); // non existent tag, ignores
        $this->assertSame('Hello guys', $t->render());

        // set and append tests
        $t->set('foo', 'Hello');
        $t->set('foo', 'Hi'); // overwrites
        $t->setHtml('foo', '<b>Hi</b>'); // overwrites
        $t->trySet('qwe', 'ignore this'); // ignores
        $t->trySetHtml('qwe', '<b>ignore</b> this'); // ignores

        $t->append('foo', ' and'); // appends
        $t->appendHtml('foo', ' <b>welcome</b> my'); // appends
        $t->tryAppend('foo', ' dear'); // appends
        $t->tryAppend('qwe', 'ignore this'); // ignores
        $t->tryAppendHtml('foo', ' and <b>smart</b>'); // appends html
        $t->tryAppendHtml('qwe', '<b>ignore</b> this'); // ignores

        $this->assertSame('<b>Hi</b> and <b>welcome</b> my dear and <b>smart</b> guys', $t->render());
    }

    /**
     * ArrayAccess test.
     */
    public function testArrayAccess()
    {
        $t = new \atk4\ui\Template('{foo}hello{/}, cruel {bar}world{/}. {foo}welcome{/}');

        $this->assertTrue(isset($t['foo']));

        $t['foo'] = 'Hi';
        $this->assertSame([1 => 'Hi'], $t['foo']); // 1 index instead of 0 because of https://bugs.php.net/bug.php?id=79844

        unset($t['foo']);
        $this->assertSame([], $t['foo']);

        $this->assertTrue(isset($t['foo'])); // it's still set even after unset - that's specific for Template
    }

    /**
     * Test eachTag.
     */
    public function testEachTag()
    {
        $t = new \atk4\ui\Template('{foo}hello{/}, {how}cruel{/how} {bar}world{/}. {foo}welcome{/}');

        // replace values in these tags
        foreach (['foo', 'bar'] as $tag) {
            $t->eachTag($tag, function ($value, $fullTag) {
                return strtoupper($value);
            });
        }
        $this->assertSame('HELLO, cruel WORLD. WELCOME', $t->render());

        // tag contains all template (for example in Lister)
        $t = new \atk4\ui\Template('{foo}hello{/}');
        $t->eachTag('foo', function ($value, $tag) {
            return strtoupper($value);
        });
        $this->assertSame('HELLO', $t->render());
    }

    /**
     * Clone region.
     */
    public function testClone()
    {
        $t = new \atk4\ui\Template('{foo}hello{/} guys');

        // clone only {foo} region
        $t1 = $t->cloneRegion('foo');
        $this->assertSame('hello', $t1->render());

        // clone all template
        $t1 = $t->cloneRegion('_top');
        $this->assertSame('hello guys', $t1->render());
    }

    /**
     * Try to load template from non existent file - exception.
     */
    public function testLoadException()
    {
        $t = new \atk4\ui\Template();
        $this->expectException(Exception::class);
        $t->load('such-file-does-not-exist.txt');
    }

    /**
     * Test renderRegion.
     */
    public function testRenderRegion()
    {
        $t = new \atk4\ui\Template('{foo}hello{/} guys');
        $this->assertSame('hello', $t->render('foo'));
    }

    public function testDollarTags()
    {
        $t = new \atk4\ui\Template('{$foo} guys and {$bar} here');
        $t->set([
            'foo' => 'Hello',
            'bar' => 'welcome',
        ]);
        $this->assertSame('Hello guys and welcome here', $t->render());
    }
}
