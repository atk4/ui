<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;

class TagTest extends TestCase
{
    public function getApp(): \Atk4\Ui\App
    {
        return new \Atk4\Ui\App(['catchExceptions' => false, 'alwaysRun' => false]);
    }

    public function assertTagRender(string $html, array $args): void
    {
        $app = $this->getApp();
        $this->assertSame($html, $app->getTag(...$args));
    }

    public function testBasic(): void
    {
        $this->assertTagRender('<b>', ['b']);
        $this->assertTagRender('<b>hello world</b>', ['b', 'hello world']);
        $this->assertTagRender('<div>', []);
    }

    public function testEscaping(): void
    {
        $this->assertTagRender('<div foo="he&quot;llo">', [null, ['foo' => 'he"llo']]);
        $this->assertTagRender('<b>bold text &gt;&gt;</b>', ['b', 'bold text >>']);
    }

    public function testElementSubstitution(): void
    {
        $this->assertTagRender('<a foo="hello">', ['a', ['foo' => 'hello']]);
        $this->assertTagRender('<a>link</a>', ['b', ['a'], 'link']);
        $this->assertTagRender('<a/>', ['b/', ['a']]);
        $this->assertTagRender('</b>', ['/b']);
        $this->assertTagRender('</a>', ['/b', ['a']]);
        $this->assertTagRender('</a>', ['/b', ['foo' => 'bar', 'a']]);
    }

    public function testAttributes(): void
    {
        $this->assertTagRender('<a>', ['a', ['foo' => false]]);
        $this->assertTagRender('<td nowrap>', ['td', ['nowrap' => true]]);
    }

    public function test3rdAttribute(): void
    {
        $this->assertTagRender('<a href="hello">', ['a', ['href' => 'hello'], null]);
        $this->assertTagRender('<a href="hello"></a>', ['a', ['href' => 'hello'], '']);
        $this->assertTagRender('<a href="hello">welcome</a>', ['a', ['href' => 'hello'], 'welcome']);
    }

    public function testNestedTags(): void
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
        $this->assertSame(
            '<a href="hello">click <i>italic</i> text</a>',
            $app->getTag('a', ['href' => 'hello'], ['click ', ['i', 'italic'], ' text'])
        );
    }
}
