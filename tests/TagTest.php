<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;

class TagTest extends TestCase
{
    public static function assertTagRender(string $html, array $args): void
    {
        $app = (new \ReflectionClass(App::class))->newInstanceWithoutConstructor();

        static::assertSame($html, $app->getTag(...$args));
    }

    public function testBasic(): void
    {
        static::assertTagRender('<b>', ['b']);
        static::assertTagRender('<b>hello world</b>', ['b', 'hello world']);
        static::assertTagRender('<div>', []);
    }

    public function testEscaping(): void
    {
        static::assertTagRender('<div foo="he&quot;llo">', [null, ['foo' => 'he"llo']]);
        static::assertTagRender('<b>bold text &gt;&gt;</b>', ['b', 'bold text >>']);
    }

    public function testElementSubstitution(): void
    {
        static::assertTagRender('<a foo="hello">', ['a', ['foo' => 'hello']]);
        static::assertTagRender('<a>link</a>', ['b', ['a'], 'link']);
        static::assertTagRender('<a/>', ['b/', ['a']]);
        static::assertTagRender('</b>', ['/b']);
        static::assertTagRender('</a>', ['/b', ['a']]);
        static::assertTagRender('</a>', ['/b', ['foo' => 'bar', 'a']]);
    }

    public function testAttributes(): void
    {
        static::assertTagRender('<a>', ['a', ['foo' => false]]);
        static::assertTagRender('<td nowrap>', ['td', ['nowrap' => true]]);
    }

    public function test3rdAttribute(): void
    {
        static::assertTagRender('<a href="hello">', ['a', ['href' => 'hello'], null]);
        static::assertTagRender('<a href="hello"></a>', ['a', ['href' => 'hello'], '']);
        static::assertTagRender('<a href="hello">welcome</a>', ['a', ['href' => 'hello'], 'welcome']);
    }

    public function testNestedTags(): void
    {
        // simply nest 1 tag
        static::assertTagRender('<a href="hello"><b>welcome</b></a>', ['a', ['href' => 'hello'], [['b', 'welcome']]]);
        static::assertTagRender('<a href="hello"><b class="red">welcome</b></a>', ['a', ['href' => 'hello'], [['b', ['class' => 'red'], 'welcome']]]);

        // nest multiple tags
        static::assertTagRender(
            '<a href="hello"><b class="red"><i class="blue">welcome</i></b></a>',
            ['a', ['href' => 'hello'], [
                ['b', ['class' => 'red'], [
                    ['i', ['class' => 'blue'], 'welcome'],
                ]],
            ]]
        );

        // this way it doesn't work, because $value of getTag is always encoded if it is a string
        static::assertTagRender(
            '<a href="hello">click <i>italic</i> text</a>',
            ['a', ['href' => 'hello'], ['click ', ['i', 'italic'], ' text']]
        );
    }
}
