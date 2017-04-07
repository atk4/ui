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

    public function test3rdAttribute()
    {
        $this->assertTagRender('<a href="hello">', ['a', ['href'=>'hello'], null]);
        $this->assertTagRender('<a href="hello"></a>', ['a', ['href'=>'hello'], '']);
        $this->assertTagRender('<a href="hello">welcome</a>', ['a', ['href'=>'hello'], 'welcome']);
    }

    public function testNestedTags()
    {
        // simply nest 1 tag
        $this->assertTagRender('<a href="hello"><b>welcome</b></a>', ['a', ['href'=>'hello'], ['b','welcome']]);

        // this way it works
        $this->assertTagRender('<a href="hello">click <i class="blue">here <b class="red">NOW</b></i></a>',
            $app->getTag('a', ['href'=>'hello'], 'click '.
                $app->getTag('i', ['class'=>'blue'], 'here '.
                    $app->getTag('b', ['class'=>'red'], 'NOW')))
        );

        // there is no way to pass 'click ' and 'here ' in parameters :(
        $this->assertTagRender('<a href="hello">click <i class="blue">here <b class="red">NOW</b></i></a>', [
            'a',
            ['href'=> 'hello'],
            [
                'i',
                [
                    'b',
                    ['class'=> 'red'],
                    'NOW',
                ],
                'class'=> 'blue',
            ],
        ]);
    }
}
