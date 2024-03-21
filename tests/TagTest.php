<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Exception;

class TagTest extends TestCase
{
    /**
     * @param array{0: string, 1?: array<0|string, string|bool>, 2?: string|array|null} $args
     */
    public static function assertTagRender(string $expectedHtml, array $args): void
    {
        $app = (new \ReflectionClass(App::class))->newInstanceWithoutConstructor();

        self::assertSame($expectedHtml, $app->getTag(...$args));
    }

    public function testBasic(): void
    {
        self::assertTagRender('<b>', ['b']);
        self::assertTagRender('<b>hello world</b>', ['b', [], 'hello world']);
    }

    public function testEscaping(): void
    {
        self::assertTagRender('<div foo="he&quot;llo">', ['div', ['foo' => 'he"llo']]);
        self::assertTagRender('<b>bold text &gt;&gt;</b>', ['b', [], 'bold text >>']);
    }

    public function testElementSubstitution(): void
    {
        self::assertTagRender('<a foo="hello">', ['a', ['foo' => 'hello']]);
        self::assertTagRender('<a>link</a>', ['b', ['a'], 'link']);
        self::assertTagRender('<hr>', ['br/', ['hr']]);
        self::assertTagRender('</b>', ['/b']);
        self::assertTagRender('</a>', ['/b', ['a']]);
        self::assertTagRender('</a>', ['/b', ['foo' => 'bar', 'a']]);
    }

    public function testAttributes(): void
    {
        self::assertTagRender('<a>', ['a', ['foo' => false]]);
        self::assertTagRender('<td nowrap="nowrap">', ['td', ['nowrap' => true]]);
    }

    public function test3rdAttribute(): void
    {
        self::assertTagRender('<a href="hello">', ['a', ['href' => 'hello'], null]);
        self::assertTagRender('<a href="hello"></a>', ['a', ['href' => 'hello'], '']);
        self::assertTagRender('<a href="hello">welcome</a>', ['a', ['href' => 'hello'], 'welcome']);
    }

    public function testNestedTags(): void
    {
        // simply nest 1 tag
        self::assertTagRender('<a href="hello"><b>welcome</b></a>', ['a', ['href' => 'hello'], [['b', [], 'welcome']]]);
        self::assertTagRender('<a href="hello"><b class="red">welcome</b></a>', ['a', ['href' => 'hello'], [['b', ['class' => 'red'], 'welcome']]]);

        // nest multiple tags
        self::assertTagRender(
            '<a href="hello"><b class="red"><i class="blue">welcome</i></b></a>',
            ['a', ['href' => 'hello'], [
                ['b', ['class' => 'red'], [
                    ['i', ['class' => 'blue'], 'welcome'],
                ]],
            ]]
        );

        // this way it doesn't work, because $value of getTag is always encoded if it is a string
        self::assertTagRender(
            '<a href="hello">click <i>italic</i> text</a>',
            ['a', ['href' => 'hello'], ['click ', ['i', [], 'italic'], ' text']]
        );
    }

    public function testEtagoEscape(): void
    {
        $v = 'foo <b>bar</b> <script>x = \'<style></style>\';"</script>" <style></script>';

        self::assertTagRender('<script>\'use strict\'; foo <b>bar</b> <script>x = \'<style></style>\';"<\/script>" <style><\/script></script>', ['script', [], $v]);
        self::assertTagRender('<style>foo <b>bar</b> <script>x = \'<style><\/style>\';"</script>" <style></script></style>', ['style', [], $v]);
        self::assertTagRender('<b>foo &lt;b&gt;bar&lt;/b&gt; &lt;script&gt;x = &apos;&lt;style&gt;&lt;/style&gt;&apos;;&quot;&lt;/script&gt;&quot; &lt;style&gt;&lt;/script&gt;</b>', ['b', [], $v]);

        self::assertTagRender('<script src="js&gt;"></script>', ['script', ['src' => 'js>'], '']);
    }

    public function testVoidTag(): void
    {
        self::assertTagRender('<br>', ['br/']);
        self::assertTagRender('<input>', ['input/', [], null]);
        self::assertTagRender('</textarea>', ['/textarea']);
    }

    public function testNotSelfClosingVoidTagException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('void tag');
        self::assertTagRender('', ['br']);
    }

    public function testClosingVoidTagException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('void tag');
        self::assertTagRender('', ['/br']);
    }

    public function testSelfClosingNonVoidTagException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('void tag');
        self::assertTagRender('', ['div/']);
    }

    public function testVoidTagWithValueException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('void tag');
        self::assertTagRender('', ['br/', [], '']);
    }
}
