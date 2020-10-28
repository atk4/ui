<?php

declare(strict_types=1);

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\ui\Exception;
use atk4\ui\HtmlTemplate;

class HtmlTemplateTest extends AtkPhpunit\TestCase
{
    /**
     * Test constructor.
     */
    public function testBasicInit()
    {
        $t = new HtmlTemplate('hello, {foo}world{/}');
        $t->set('foo', 'bar');

        $this->assertSame('hello, bar', $t->renderToHtml());
    }

    /**
     * Test getTagRef().
     */
    public function testGetTagRef()
    {
        // top tag
        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $t1 = &$this->callProtected($t, 'getTagRef', '_top');
        $this->assertSame(['foo#0' => ['hello'], ', cruel ', 'bar#0' => ['world'], '. ', 'foo#1' => ['hello']], $t1);

        $t1 = ['good bye']; // will change $t->template because it's by reference
        $this->assertSame(['good bye'], $this->getProtected($t, 'template'));

        // any tag
        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
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
        $t = new HtmlTemplate('{foo}hello{/}');
        $this->expectException(Exception::class);
        $this->callProtected($t, 'getTagRef', 'bar'); // not existent tag
    }

    /**
     * Test getTagRefs().
     */
    public function testGetTagRefs()
    {
        // top tag
        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello2{/}');
        $t1 = $this->callProtected($t, 'getTagRefs', '_top');
        $this->assertSame([['foo#0' => ['hello'], ', cruel ', 'bar#0' => ['world'], '. ', 'foo#1' => ['hello2']]], $t1);

        $t1[0] = ['good bye']; // will change $t->template because it's by reference
        $this->assertSame(['good bye'], $this->getProtected($t, 'template'));

        // any tag
        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello2{/}');
        $t2 = $this->callProtected($t, 'getTagRefs', 'foo');
        $this->assertSame([['hello'], ['hello2']], $t2);
        $t2[1] = ['good bye']; // will change $t->template last "foo" tag because it's by reference
        $this->assertSame(['foo#0' => ['hello'], ', cruel ', 'bar#0' => ['world'], '. ', 'foo#1' => ['good bye']], $this->getProtected($t, 'template'));

        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello2{/}');
        $t3 = $this->callProtected($t, 'getTagRefs', 'bar');
        $this->assertSame([['world']], $t3);
        $t3[0] = ['planet']; // will change $t->template last "foo" tag because it's by reference
        $this->assertSame(['foo#0' => ['hello'], ', cruel ', 'bar#0' => ['planet'], '. ', 'foo#1' => ['hello2']], $this->getProtected($t, 'template'));
    }

    /**
     * Non existent template - throw exception.
     */
    public function testBadTemplate1()
    {
        $t = new HtmlTemplate();
        $this->expectException(Exception::class);
        $t->loadFromFile('bad_template_file');
    }

    /**
     * Non existent template - no exception.
     */
    public function testBadTemplate2()
    {
        $t = new HtmlTemplate();
        $this->assertFalse($t->tryLoadFromFile('bad_template_file'));
    }

    /**
     * Exception in getTagRefs().
     */
    public function testGetTagRefsException()
    {
        $t = new HtmlTemplate('{foo}hello{/}');
        $this->expectException(Exception::class);
        $this->callProtected($t, 'getTagRefs', 'bar'); // not existent tag
    }

    /**
     * Test hasTag().
     */
    public function testHasTag()
    {
        $t = new HtmlTemplate('{foo}hello{/}, cruel {bar}world{/}. {foo}hello{/}');
        $this->assertTrue($t->hasTag(['foo', 'bar'])); // all tags exist
        $this->assertFalse($t->hasTag(['foo', 'bar', 'qwe'])); // qwe tag does not exist
    }

    /**
     * Test set() exception.
     */
    public function testSetException1()
    {
        $t = new HtmlTemplate('{foo}hello{/} guys');
        $this->expectException(Exception::class);
        $t->set('qwe', 'Hello'); // not existent tag
    }

    /**
     * Test set() exception.
     */
    public function testSetException2()
    {
        $t = new HtmlTemplate('{foo}hello{/} guys');
        $this->expectException(Exception::class);
        $t->set('foo', new \StdClass()); // bad value
    }

    /**
     * Test set, append, tryAppend, tryDangerouslyAppendHtml, del, tryDel.
     */
    public function testSetAppendDel()
    {
        $t = new HtmlTemplate('{foo}hello{/} guys');

        // del tests
        $t->set('foo', 'Hello');
        $t->del('foo');
        $this->assertSame(' guys', $t->renderToHtml());
        $t->set('foo', 'Hello');
        $t->tryDel('qwe'); // non existent tag, ignores
        $this->assertSame('Hello guys', $t->renderToHtml());

        // set and append tests
        $t->set('foo', 'Hello');
        $t->set('foo', 'Hi'); // overwrites
        $t->dangerouslySetHtml('foo', '<b>Hi</b>'); // overwrites
        $t->trySet('qwe', 'ignore this'); // ignores
        $t->tryDangerouslySetHtml('qwe', '<b>ignore</b> this'); // ignores

        $t->append('foo', ' and'); // appends
        $t->dangerouslyAppendHtml('foo', ' <b>welcome</b> my'); // appends
        $t->tryAppend('foo', ' dear'); // appends
        $t->tryAppend('qwe', 'ignore this'); // ignores
        $t->tryDangerouslyAppendHtml('foo', ' and <b>smart</b>'); // appends html
        $t->tryDangerouslyAppendHtml('qwe', '<b>ignore</b> this'); // ignores

        $this->assertSame('<b>Hi</b> and <b>welcome</b> my dear and <b>smart</b> guys', $t->renderToHtml());
    }

    /**
     * Test eachTag.
     */
    public function testEachTag()
    {
        $t = new HtmlTemplate('{foo}hello{/}, {how}cruel{/how} {bar}world{/}. {foo}welcome{/}');

        // replace values in these tags
        foreach (['foo', 'bar'] as $tag) {
            $t->eachTag($tag, function ($value, $fullTag) {
                return strtoupper($value);
            });
        }
        $this->assertSame('HELLO, cruel WORLD. WELCOME', $t->renderToHtml());

        // tag contains all template (for example in Lister)
        $t = new HtmlTemplate('{foo}hello{/}');
        $t->eachTag('foo', function ($value, $tag) {
            return strtoupper($value);
        });
        $this->assertSame('HELLO', $t->renderToHtml());
    }

    /**
     * Clone region.
     */
    public function testClone()
    {
        $t = new HtmlTemplate('{foo}hello{/} guys');

        // clone only {foo} region
        $t1 = $t->cloneRegion('foo');
        $this->assertSame('hello', $t1->renderToHtml());

        // clone all template
        $t1 = $t->cloneRegion('_top');
        $this->assertSame('hello guys', $t1->renderToHtml());
    }

    /**
     * Try to load template from non existent file - exception.
     */
    public function testLoadException()
    {
        $t = new HtmlTemplate();
        $this->expectException(Exception::class);
        $t->loadFromFile('such-file-does-not-exist.txt');
    }

    /**
     * Test renderRegion.
     */
    public function testRenderRegion()
    {
        $t = new HtmlTemplate('{foo}hello{/} guys');
        $this->assertSame('hello', $t->renderToHtml('foo'));
    }

    public function testDollarTags()
    {
        $t = new HtmlTemplate('{$foo} guys and {$bar} here');
        $t->set([
            'foo' => 'Hello',
            'bar' => 'welcome',
        ]);
        $this->assertSame('Hello guys and welcome here', $t->renderToHtml());
    }
}
