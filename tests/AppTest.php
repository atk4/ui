<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Exception\LateOutputError;
use Atk4\Ui\HtmlTemplate;
use Nyholm\Psr7\Factory\Psr17Factory;

class AppTest extends TestCase
{
    use CreateAppTrait;

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

    public function provideUrlBuildingCases(): iterable
    {
        // simple cases
        yield ['/', [], [], '/index.php'];
        yield ['/test/', [], [], '/test/index.php'];

        // simple cases with query args in request
        yield ['/?test=atk4', [], [], '/index.php?test=atk4'];
        yield ['/test/?test=atk4', [], [], '/test/index.php?test=atk4'];

        // simple cases with extra query args in request
        yield ['/?test=atk4', [], ['extra_args' => 'atk4'], '/index.php?extra_args=atk4&test=atk4'];
        yield ['/test/?test=atk4', [], ['extra_args' => 'atk4'], '/test/index.php?extra_args=atk4&test=atk4'];

        // simple cases with page as string
        yield ['/', 'test', [], 'test.php'];
        yield ['/', 'test/test/a', [], 'test/test/a.php'];
        yield ['/', 'test/index', [], 'test/index.php'];
        yield ['/test/', 'test/index', [], 'test/index.php'];
        yield ['/', '/index.php', [], '/index.php'];
        yield ['/request-url', '/request-url', [], '/request-url.php'];
        yield ['/request-url/', '/request-url/', [], '/request-url/index.php'];
        yield ['/test/', '/test/', [], '/test/index.php'];

        // simple cases with page as array with 0 => string
        yield ['/', ['test'], [], 'test.php'];
        yield ['/', ['test/test/a'], [], 'test/test/a.php'];
        yield ['/test/', ['test/index'], [], 'test/index.php'];

        // query args in page cases
        yield ['/', ['test', 'extra_args' => 'atk4'], [], 'test.php?extra_args=atk4'];
        yield ['/', ['test/test/a', 'extra_args' => 'atk4'], [], 'test/test/a.php?extra_args=atk4'];
        yield ['/test/', ['test/index', 'extra_args' => 'atk4'], [], 'test/index.php?extra_args=atk4'];

        // extra query args cases
        yield ['/', ['test'], ['extra_args' => 'atk4'], 'test.php?extra_args=atk4'];
        yield ['/', ['test/test/a'], ['extra_args' => 'atk4'], 'test/test/a.php?extra_args=atk4'];
        yield ['/test/', ['test/index'], ['extra_args' => 'atk4'], 'test/index.php?extra_args=atk4'];

        // query args in page cases and query args in request cases and extra query args cases
        yield ['/?extra_args=atk4&query_args=atk4&page_args=atk4', ['test', 'page_args' => 'atk4'], ['extra_args' => 'atk4'], 'test.php?extra_args=atk4&query_args=atk4&page_args=atk4'];
        yield ['/?extra_args=atk4&query_args=atk4&page_args=atk4', ['test/test/a', 'page_args' => 'atk4'], ['extra_args' => 'atk4'], 'test/test/a.php?extra_args=atk4&query_args=atk4&page_args=atk4'];
        yield ['/test/?extra_args=atk4&query_args=atk4&page_args=atk4', ['test/index', 'page_args' => 'atk4'], ['extra_args' => 'atk4'], 'test/index.php?extra_args=atk4&query_args=atk4&page_args=atk4'];
        yield ['/?extra_args=atk4&query_args=atk4&page_args=atk4', ['test', 'page_args' => 'atk4', 'check_unset_page' => false], ['extra_args' => 'atk4'], 'test.php?extra_args=atk4&query_args=atk4&page_args=atk4'];
    }

    /**
     * @dataProvider provideUrlBuildingCases
     *
     * @param string|array<0|string, string|int|false> $page
     * @param array<string, string>                    $extraRequestUrlArgs
     */
    public function testUrlBuilding(string $requestUrl, $page, array $extraRequestUrlArgs, string $exceptedStd): void
    {
        $factory = new Psr17Factory();
        $request = $factory->createServerRequest('GET', 'http://127.0.0.1' . $requestUrl);

        $stickyGetArguments = array_merge([
            '__atk_json' => false,
            '__atk_tab' => false,
        ], $request->getQueryParams());

        $app = $this->createApp([
            'request' => $request,
            'stickyGetArguments' => $stickyGetArguments,
        ]);

        self::assertSame($exceptedStd, $app->url($page, $extraRequestUrlArgs));
        self::assertSame($exceptedStd, $app->jsUrl($page, $extraRequestUrlArgs));

        $makeExpectedUrlFx = static function (string $indexPage, string $ext) use ($page, $exceptedStd) {
            return preg_replace_callback('~((?<=/)index)?(\.php)(?=\?|$)~', static function ($matches) use ($page, $indexPage, $ext) {
                if ($matches[1] !== '' && !preg_match('~/index(\.php)?(?=\?|$)~', is_string($page) ? $page : ($page[0] ?? ''))) {
                    $matches[1] = $indexPage;
                }
                if ($matches[2] !== '' && !preg_match('~\.php(?=\?|$)~', is_string($page) ? $page : ($page[0] ?? ''))) {
                    $matches[2] = $ext;
                }

                return $matches[1] . $matches[2];
            }, $exceptedStd, 1);
        };

        $app = $this->createApp([
            'request' => $request,
            'stickyGetArguments' => $stickyGetArguments,
            'urlBuildingIndexPage' => 'default',
            'urlBuildingExt' => '.php8',
        ]);

        $exceptedCustom = $makeExpectedUrlFx('default', '.php8');
        self::assertSame($exceptedCustom, $app->url($page, $extraRequestUrlArgs));
        self::assertSame($exceptedCustom, $app->jsUrl($page, $extraRequestUrlArgs));

        $app = $this->createApp([
            'request' => $request,
            'stickyGetArguments' => $stickyGetArguments,
            'urlBuildingIndexPage' => '',
            'urlBuildingExt' => '',
        ]);
        $exceptedRouting = $makeExpectedUrlFx('', '');
        self::assertSame($exceptedRouting, $app->url($page, $extraRequestUrlArgs));
        self::assertSame($exceptedRouting, $app->jsUrl($page, $extraRequestUrlArgs));
    }
}
