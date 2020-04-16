<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;

class TagTest extends AtkPhpunit\TestCase
{
    public function getApp()
    {
        return new \atk4\ui\App(['catch_exceptions' => false, 'always_run' => false]);
    }

    public function assertTagRender($html, $args)
    {
        $app = $this->getApp();
        $this->assertEquals($html, $app->getTag(...$args));
    }

    public function testBasic()
    {
        $this->assertTagRender('<b>', ['b']);
        $this->assertTagRender('<b>hello world</b>', ['b', 'hello world']);
        $this->assertTagRender('<div>', []);
    }

    public function testEscaping()
    {
        $this->assertTagRender('<div foo="he&quot;llo">', [['foo' => 'he"llo']]);
        $this->assertTagRender('<b>bold text &gt;&gt;</b>', ['b', 'bold text >>']);
    }

    public function testElementSubstitution()
    {
        $this->assertTagRender('<a foo="hello">', [['a', 'foo' => 'hello']]);
        $this->assertTagRender('<a>link</a>', ['b', ['a'], 'link']);
        $this->assertTagRender('<a/>', ['b/', ['a']]);
        $this->assertTagRender('</b>', ['/b']);
        $this->assertTagRender('</a>', ['/b', ['a']]);
        $this->assertTagRender('</a>', ['/b', ['foo' => 'bar', 'a']]);
    }

    public function testAttributes()
    {
        $this->assertTagRender('<a>', ['a', ['foo' => false]]);
        $this->assertTagRender('<td nowrap>', ['td', ['nowrap' => true]]);
    }

    public function test3rdAttribute()
    {
        $this->assertTagRender('<a href="hello">', ['a', ['href' => 'hello'], null]);
        $this->assertTagRender('<a href="hello"></a>', ['a', ['href' => 'hello'], '']);
        $this->assertTagRender('<a href="hello">welcome</a>', ['a', ['href' => 'hello'], 'welcome']);
    }

    public function testNestedTags()
    {
        // simply nest 1 tag
        $this->assertTagRender('<a href="hello"><b>welcome</b></a>', ['a', ['href' => 'hello'], [['b', 'welcome']]]);
        $this->assertTagRender('<a href="hello"><b class="red">welcome</b></a>', ['a', ['href' => 'hello'], [['b', ['class' => 'red'], 'welcome']]]);

        // nest multiple tags
        $this->assertTagRender(
            '<a href="hello"><b class="red"><i class="blue">welcome</i></b></a>',
            ['a', ['href' => 'hello'], [
                ['b', ['class' => 'red'], [
                    ['i', ['class' => 'blue'], 'welcome'],
                ]],
            ]]
        );

        // this way it doesn't work, because $value of getTag is always encoded if it is a string
        $app = $this->getApp();
        $this->assertEquals(
            '<a href="hello">click <i>italic</i> text</a>',
            $app->getTag('a', ['href' => 'hello'], ['click ', ['i', 'italic'], ' text'])
        );
    }
}
