<?php

namespace atk4\ui\tests;

class TagTest extends \atk4\core\PHPUnit_AgileTestCase
{
    public function assertTagRender($html, $args)
    {
        $app = new \atk4\ui\App(['catch_exceptions'=>false, 'always_run'=>false]);
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
        $this->assertTagRender('<div foo="he&quot;llo">', [['foo'=>'he"llo']]);
        $this->assertTagRender('<b>bold text &gt;&gt;</b>', ['b', 'bold text >>']);
    }

    public function testElementSubstitution()
    {
        $this->assertTagRender('<a foo="hello">', [['a', 'foo'=>'hello']]);
        $this->assertTagRender('<a>link</a>', ['b', ['a'], 'link']);
        $this->assertTagRender('<a/>', ['b/', ['a']]);
        $this->assertTagRender('</b>', ['/b']);
        $this->assertTagRender('</a>', ['/b', ['a']]);
        $this->assertTagRender('</a>', ['/b', ['foo'=>'bar', 'a']]);
    }

    public function testAttributes()
    {
        $this->assertTagRender('<a>', ['a', ['foo'=>false]]);
        $this->assertTagRender('<td nowrap>', ['td', ['nowrap'=>true]]);
    }
}
