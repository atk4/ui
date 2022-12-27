<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Exception;

class TagTest extends TestCase
{
    public static function assertTagRender(string $expectedHtml, array $args): void
    {
        $app = (new \ReflectionClass(App::class))->newInstanceWithoutConstructor();

        static::assertSame($expectedHtml, $app->getTag(...$args));
    }

    public function testBasic(): void
    {
        static::assertTagRender('<b>', ['b']);
        static::assertTagRender('<b>hello world</b>', ['b', [], 'hello world']);
        static::assertTagRender('<div>', []);
    }

    public function testEscaping(): void
    {
        static::assertTagRender('<div foo="he&quot;llo">', [null, ['foo' => 'he"llo']]);
        static::assertTagRender('<b>bold text &gt;&gt;</b>', ['b', [], 'bold text >>']);
    }

    public function testElementSubstitution(): void
    {
        static::assertTagRender('<a foo="hello">', ['a', ['foo' => 'hello']]);
        static::assertTagRender('<a>link</a>', ['b', ['a'], 'link']);
        static::assertTagRender('<hr>', ['br/', ['hr']]);
        static::assertTagRender('</b>', ['/b']);
        static::assertTagRender('</a>', ['/b', ['a']]);
        static::assertTagRender('</a>', ['/b', ['foo' => 'bar', 'a']]);
    }

    public function testAttributes(): void
    {
        static::assertTagRender('<a>', ['a', ['foo' => false]]);
        static::assertTagRender('<td nowrap="nowrap">', ['td', ['nowrap' => true]]);
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
        static::assertTagRender('<a href="hello"><b>welcome</b></a>', ['a', ['href' => 'hello'], [['b', [], 'welcome']]]);
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
            ['a', ['href' => 'hello'], ['click ', ['i', [], 'italic'], ' text']]
        );
    }

    public function testEtagoEscape(): void
    {
        $v = 'foo <b>bar</b> <script>x = \'<style></style>\';"</script>" <style></script>';

        static::assertTagRender('<script>\'use strict\'; foo <b>bar</b> <script>x = \'<style></style>\';"<\/script>" <style><\/script></script>', ['script', [], $v]);
        static::assertTagRender('<style>foo <b>bar</b> <script>x = \'<style><\/style>\';"</script>" <style></script></style>', ['style', [], $v]);
        static::assertTagRender('<b>foo &lt;b&gt;bar&lt;/b&gt; &lt;script&gt;x = &apos;&lt;style&gt;&lt;/style&gt;&apos;;&quot;&lt;/script&gt;&quot; &lt;style&gt;&lt;/script&gt;</b>', ['b', [], $v]);

        static::assertTagRender('<script src="js&gt;"></script>', ['script', ['src' => 'js>'], '']);
    }

    public function testVoidTag(): void
    {
        static::assertTagRender('<br>', ['br/']);
        static::assertTagRender('<input>', ['input/', [], null]);
        static::assertTagRender('</textarea>', ['/textarea']);
    }

    public function testNotSelfClosingVoidTagException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('void tag');
        static::assertTagRender('', ['br']);
    }

    public function testClosingVoidTagException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('void tag');
        static::assertTagRender('', ['/br']);
    }

    public function testSelfClosingNonVoidTagException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('void tag');
        static::assertTagRender('', ['div/']);
    }

    public function testVoidTagWithValueException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('void tag');
        static::assertTagRender('', ['br/', [], '']);
    }
}
