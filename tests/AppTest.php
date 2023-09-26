<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Exception;
use Atk4\Ui\Exception\LateOutputError;
use Atk4\Ui\HtmlTemplate;
use Nyholm\Psr7\Factory\Psr17Factory;

class AppTest extends TestCase
{
    use CreateAppTrait;

    public function testHasRequestQueryParam(): void
    {
        $app = $this->createApp(['request' => (new Psr17Factory())->createServerRequest('GET', '/?a=1&b=0&c=&d')]);

        self::assertTrue($app->hasRequestQueryParam('a'));
        self::assertTrue($app->hasRequestQueryParam('b'));
        self::assertTrue($app->hasRequestQueryParam('c'));
        self::assertTrue($app->hasRequestQueryParam('d'));
        self::assertFalse($app->hasRequestQueryParam('z'));

        self::assertSame('1', $app->getRequestQueryParam('a'));
        self::assertSame('0', $app->getRequestQueryParam('b'));
        self::assertSame('', $app->getRequestQueryParam('c'));
        self::assertSame('', $app->getRequestQueryParam('d'));
        self::assertNull($app->tryGetRequestQueryParam('z'));
    }

    public function testNoRequestQueryParamException(): void
    {
        $app = $this->createApp();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('GET param does not exist');
        $app->getRequestQueryParam('foo');
    }

    public function testNoRequestPostParamException(): void
    {
        $app = $this->createApp();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('POST param does not exist');
        $app->getRequestPostParam('foo');
    }

    public function testNoRequestUploadedFileException(): void
    {
        $app = $this->createApp();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('FILES upload does not exist');
        $app->getRequestUploadedFile('foo');
    }

    public function testTemplateClassDefault(): void
    {
        $app = $this->createApp();

        self::assertInstanceOf(
            HtmlTemplate::class,
            $app->loadTemplate('html.html')
        );
    }

    public function testTemplateClassCustom(): void
    {
        $anotherTemplateClass = get_class(new class() extends HtmlTemplate {});

        $app = $this->createApp([
            'templateClass' => $anotherTemplateClass,
        ]);

        self::assertInstanceOf($anotherTemplateClass, $app->loadTemplate('html.html'));
    }

    public function testHeaderNormalize(): void
    {
        $app = $this->createApp();
        $app->setResponseHeader('cache-control', '');

        $app->setResponseHeader('content-type', 'Xy');
        self::assertSame(['Content-Type' => ['Xy']], $app->getResponse()->getHeaders());

        $app->setResponseHeader('CONTENT-type', 'xY');
        self::assertSame(['Content-Type' => ['xY']], $app->getResponse()->getHeaders());

        $app->setResponseHeader('content-TYPE', '');
        self::assertSame([], $app->getResponse()->getHeaders());
    }

    public function testUnexpectedOutputLateError(): void
    {
        $app = $this->createApp();

        ob_start();
        $testStr = 'direct output test';
        try {
            echo $testStr;

            $this->expectException(LateOutputError::class);
            $this->expectExceptionMessage('Unexpected output detected');
            $app->terminateHtml('');
        } finally {
            self::assertSame($testStr, ob_get_contents());
            ob_end_clean();
        }
    }

    public function testEmptyRequestPathException(): void
    {
        $request = (new Psr17Factory())->createServerRequest('GET', '');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Request URL path must always start with \'/\'');
        $this->createApp(['request' => $request]);
    }

    public function provideUrlCases(): iterable
    {
        foreach (['/', '/page.html', '/d/', '/0/index.php'] as $requestPage) {
            yield [$requestPage, [], ['x'], [], 'x.php'];
            yield [$requestPage, [], ['/x'], [], '/x.php'];
            yield [$requestPage, [], ['x/y/z'], [], 'x/y/z.php'];
            yield [$requestPage, [], ['/x/y/z'], [], '/x/y/z.php'];
            yield [$requestPage, [], ['0'], [], '0.php'];
            yield [$requestPage, [], ['x.php'], [], 'x.php'];
            yield [$requestPage, [], ['x.html'], [], 'x.html'];
            yield [$requestPage, [], ['index'], [], 'index.php'];
            yield [$requestPage, [], ['index.php'], [], 'index.php'];
            yield [$requestPage . '?u=U', [], ['x'], [], 'x.php'];
            yield [$requestPage . '?index.php', [], ['x'], [], 'x.php'];

            // /w page autoindex
            yield [$requestPage, [], ['/'], [], '/index.php'];
            yield [$requestPage, [], ['/0/0/'], [], '/0/0/index.php'];
            yield [$requestPage, [], ['a.b c/'], [], 'a.b c/index.php'];
            yield [$requestPage . '?u=U', [], ['x/'], [], 'x/index.php'];

            // /w page args
            yield [$requestPage, [], ['x', 'foo' => 'a'], [], 'x.php?foo=a'];
            yield [$requestPage, [], ['x', 'foo' => 'a', 'bar' => '0'], [], 'x.php?foo=a&bar=0'];
            yield [$requestPage, [], ['x', 'foo' => ''], [], 'x.php?foo='];
            yield [$requestPage, [], ['x', 'foo' => 'a b'], [], 'x.php?foo=a%20b'];
            yield [$requestPage, [], ['x.html', 'foo' => 'index.php'], [], 'x.html?foo=index.php'];
            yield [$requestPage . '?u=U', [], ['x', 'foo' => 'a'], [], 'x.php?foo=a'];

            // /w extra args
            yield [$requestPage, [], ['x'], ['foo' => 'a'], 'x.php?foo=a'];
            yield [$requestPage, [], ['x'], ['foo' => 'a', 'bar' => '0'], 'x.php?foo=a&bar=0'];
            yield [$requestPage, [], ['x'], ['foo' => ''], 'x.php?foo='];
            yield [$requestPage, [], ['x'], ['foo' => 'a b'], 'x.php?foo=a%20b'];
            yield [$requestPage . '?u=U', [], ['x'], ['foo' => 'a'], 'x.php?foo=a'];
        }

        // /w sticky args
        yield ['/?u=U&v=V', ['v' => true], ['x'], [], 'x.php?v=V'];
        yield ['/?u=U&v=V', ['v' => true], ['x', 'foo' => 'a'], [], 'x.php?v=V&foo=a'];
        yield ['/', ['v' => true], ['x'], [], 'x.php'];
        yield ['/?v=V', ['v' => false], ['x'], [], 'x.php'];
        yield ['/', ['v' => false], ['x', 'v' => 'page'], [], 'x.php?v=page'];
        yield ['/', ['v' => false], ['x'], ['v' => 'extra'], 'x.php'];

        // /wo page path
        yield ['/x', [], [], [], '/x.php'];
        yield ['/d/x.html', [], ['foo' => 'a'], [], '/d/x.html?foo=a'];
        yield ['/?u=U&v=V', ['v' => true], [], [], '/index.php?v=V'];

        // args priority
        yield ['/', [], ['x', 'foo' => 'page'], ['foo' => 'extra'], 'x.php?foo=page'];
        yield ['/?foo=sticky', ['foo' => true], ['x', 'foo' => 'page'], ['foo' => 'extra'], 'x.php?foo=page'];
        yield ['/?foo=sticky', ['foo' => true], ['x'], ['foo' => 'extra'], 'x.php?foo=sticky'];
        yield ['/', ['foo' => true], ['x'], ['foo' => 'extra'], 'x.php'];
    }

    /**
     * @dataProvider provideUrlCases
     *
     * @param array<string, bool>               $appStickyGetArguments
     * @param array<0|string, string|int|false> $page
     * @param array<string, string>             $extraRequestUrlArgs
     */
    public function testUrl(string $requestUrl, array $appStickyGetArguments, array $page, array $extraRequestUrlArgs, string $expectedUrl): void
    {
        $request = (new Psr17Factory())->createServerRequest('GET', $requestUrl);

        $app = $this->createApp([
            'request' => $request,
            'stickyGetArguments' => $appStickyGetArguments,
        ]);
        self::assertSame($expectedUrl, $app->url($page, $extraRequestUrlArgs));
        $pageAssocOnly = array_diff_key($page, [true]);
        self::assertSame($expectedUrl, $app->url(($page[0] ?? '') . (count($pageAssocOnly) > 0 ? '?' . implode('&', array_map(static fn ($k) => $k . '=' . $pageAssocOnly[$k], array_keys($pageAssocOnly))) : ''), $extraRequestUrlArgs));
        self::assertSame($expectedUrl, $app->jsUrl($page, array_merge(['__atk_json' => null], $extraRequestUrlArgs)));

        $makeExpectedUrlFx = static function (string $indexPage, string $ext) use ($page, $expectedUrl) {
            return preg_replace_callback('~^[^?]*?\K([^/?]*)(\.php)(?=\?|$)~', static function ($matches) use ($page, $indexPage, $ext) {
                if ($matches[1] === 'index' && !preg_match('~^([^?]*?/)?index(\.php)?(?=\?|$)~', $page[0] ?? '')) {
                    $matches[1] = $indexPage;
                }
                if (!preg_match('~^[^?]*?\.php(?=\?|$)~', $page[0] ?? '')) {
                    $matches[2] = $matches[1] !== '' ? $ext : '';
                }

                return $matches[1] . $matches[2];
            }, $expectedUrl);
        };

        $app = $this->createApp([
            'request' => $request,
            'stickyGetArguments' => $appStickyGetArguments,
            'urlBuildingIndexPage' => 'default',
            'urlBuildingExt' => '.php8',
        ]);
        $expectedUrlCustom = $makeExpectedUrlFx('default', '.php8');
        self::assertSame($expectedUrlCustom, $app->url($page, $extraRequestUrlArgs));

        $app = $this->createApp([
            'request' => $request,
            'stickyGetArguments' => $appStickyGetArguments,
            'urlBuildingIndexPage' => '',
            'urlBuildingExt' => '',
        ]);
        $expectedUrlAutoindex = $makeExpectedUrlFx('', '');
        self::assertSame($expectedUrlAutoindex, $app->url($page, $extraRequestUrlArgs));

        $app = $this->createApp([
            'request' => $request,
            'stickyGetArguments' => $appStickyGetArguments,
            'urlBuildingIndexPage' => '',
            'urlBuildingExt' => '.html',
        ]);
        $expectedUrlAutoindex2 = $makeExpectedUrlFx('', '.html');
        self::assertSame($expectedUrlAutoindex2, $app->url($page, $extraRequestUrlArgs));
    }
}
