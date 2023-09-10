<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
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
        return [
            // simple cases
            [[], false, [], '/', '/index.php', '/default.php8', '/'],
            [[], false, [], '/test/', '/test/index.php', '/test/default.php8', '/test/'],
            [[], true, [], '/', '/index.php', '/default.php8', '/'],
            [[], true, [], '/test/', '/test/index.php', '/test/default.php8', '/test/'],

            // simple cases with query args in request
            [[], false, [], '/?test=atk4', '/index.php?test=atk4', '/default.php8?test=atk4', '/?test=atk4'],
            [[], false, [], '/test/?test=atk4', '/test/index.php?test=atk4', '/test/default.php8?test=atk4', '/test/?test=atk4'],
            [[], true, [], '/?test=atk4', '/index.php?test=atk4', '/default.php8?test=atk4', '/?test=atk4'],
            [[], true, [], '/test/?test=atk4', '/test/index.php?test=atk4', '/test/default.php8?test=atk4', '/test/?test=atk4'],

            // simple cases with extra query args in request
            [[], false, ['extra_args' => 'atk4'], '/?test=atk4', '/index.php?extra_args=atk4&test=atk4', '/default.php8?extra_args=atk4&test=atk4', '/?extra_args=atk4&test=atk4'],
            [[], false, ['extra_args' => 'atk4'], '/test/?test=atk4', '/test/index.php?extra_args=atk4&test=atk4', '/test/default.php8?extra_args=atk4&test=atk4', '/test/?extra_args=atk4&test=atk4'],
            [[], true, ['extra_args' => 'atk4'], '/?test=atk4', '/index.php?extra_args=atk4&test=atk4', '/default.php8?extra_args=atk4&test=atk4', '/?extra_args=atk4&test=atk4'],
            [[], true, ['extra_args' => 'atk4'], '/test/?test=atk4', '/test/index.php?extra_args=atk4&test=atk4', '/test/default.php8?extra_args=atk4&test=atk4', '/test/?extra_args=atk4&test=atk4'],

            // simple cases with page as string
            ['test', false, [], '/', 'test.php', 'test.php8', 'test'],
            ['test/test/a', false, [], '/', 'test/test/a.php', 'test/test/a.php8', 'test/test/a'],
            ['test/index', false, [], '/', 'test/index.php', 'test/index.php8', 'test/index'],
            ['test/index', false, [], '/test/', 'test/index.php', 'test/index.php8', 'test/index'],
            ['test', true, [], '/', '/index.php', '/default.php8', '/'],
            ['test', true, [], '/request-url', '/request-url.php', '/request-url.php8', '/request-url'],
            ['test', true, [], '/request-url/', '/request-url/index.php', '/request-url/default.php8', '/request-url/'],
            ['test/index', true, [], '/test/', '/test/index.php', '/test/default.php8', '/test/'],
            ['test', false, [], '/', 'test.php', 'test.php8', 'test'],

            // simple cases with page as array with 0 => string
            [['test'], false, [], '/', 'test.php', 'test.php8', 'test'],
            [['test/test/a'], false, [], '/', 'test/test/a.php', 'test/test/a.php8', 'test/test/a'],
            [['test/index'], false, [], '/test/', 'test/index.php', 'test/index.php8', 'test/index'],
            [['test'], true, [], '/', '/index.php', '/default.php8', '/'],
            [['test'], true, [], '/request-url', '/request-url.php', '/request-url.php8', '/request-url'],
            [['test'], true, [], '/request-url/', '/request-url/index.php', '/request-url/default.php8', '/request-url/'],
            [['test/index'], true, [], '/test/', '/test/index.php', '/test/default.php8', '/test/'],
            [['test'], false, [], '/', 'test.php', 'test.php8', 'test'],

            // query args in page cases
            [['test', 'extra_args' => 'atk4'], false, [], '/', 'test.php?extra_args=atk4', 'test.php8?extra_args=atk4', 'test?extra_args=atk4'],
            [['test/test/a', 'extra_args' => 'atk4'], false, [], '/', 'test/test/a.php?extra_args=atk4', 'test/test/a.php8?extra_args=atk4', 'test/test/a?extra_args=atk4'],
            [['test/index', 'extra_args' => 'atk4'], false, [], '/test/', 'test/index.php?extra_args=atk4', 'test/index.php8?extra_args=atk4', 'test/index?extra_args=atk4'],
            [['test', 'extra_args' => 'atk4'], true, [], '/', '/index.php', '/default.php8', '/'],
            [['test', 'extra_args' => 'atk4'], true, [], '/request-url', '/request-url.php', '/request-url.php8', '/request-url'],
            [['test', 'extra_args' => 'atk4'], true, [], '/request-url/', '/request-url/index.php', '/request-url/default.php8', '/request-url/'],
            [['test/index', 'extra_args' => 'atk4'], true, [], '/test/', '/test/index.php', '/test/default.php8', '/test/'],
            [['test', 'extra_args' => 'atk4'], false, [], '/', 'test.php?extra_args=atk4', 'test.php8?extra_args=atk4', 'test?extra_args=atk4'],

            // extra query args cases
            [['test'], false, ['extra_args' => 'atk4'], '/', 'test.php?extra_args=atk4', 'test.php8?extra_args=atk4', 'test?extra_args=atk4'],
            [['test/test/a'], false, ['extra_args' => 'atk4'], '/', 'test/test/a.php?extra_args=atk4', 'test/test/a.php8?extra_args=atk4', 'test/test/a?extra_args=atk4'],
            [['test/index'], false, ['extra_args' => 'atk4'], '/test/', 'test/index.php?extra_args=atk4', 'test/index.php8?extra_args=atk4', 'test/index?extra_args=atk4'],
            [['test'], true, ['extra_args' => 'atk4'], '/', '/index.php?extra_args=atk4', '/default.php8?extra_args=atk4', '/?extra_args=atk4'],
            [['test'], true, ['extra_args' => 'atk4'], '/request-url', '/request-url.php?extra_args=atk4', '/request-url.php8?extra_args=atk4', '/request-url?extra_args=atk4'],
            [['test'], true, ['extra_args' => 'atk4'], '/request-url/', '/request-url/index.php?extra_args=atk4', '/request-url/default.php8?extra_args=atk4', '/request-url/?extra_args=atk4'],
            [['test/index'], true, ['extra_args' => 'atk4'], '/test/', '/test/index.php?extra_args=atk4', '/test/default.php8?extra_args=atk4', '/test/?extra_args=atk4'],
            [['test'], false, ['extra_args' => 'atk4'], '/', 'test.php?extra_args=atk4', 'test.php8?extra_args=atk4', 'test?extra_args=atk4'],

            // query args in page cases and query args in request cases and extra query args cases
            [['test', 'page_args' => 'atk4'], false, ['extra_args' => 'atk4'], '/?extra_args=atk4&query_args=atk4&page_args=atk4', 'test.php?extra_args=atk4&query_args=atk4&page_args=atk4', 'test.php8?extra_args=atk4&query_args=atk4&page_args=atk4', 'test?extra_args=atk4&query_args=atk4&page_args=atk4'],
            [['test/test/a', 'page_args' => 'atk4'], false, ['extra_args' => 'atk4'], '/?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/test/a.php?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/test/a.php8?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/test/a?extra_args=atk4&query_args=atk4&page_args=atk4'],
            [['test/index', 'page_args' => 'atk4'], false, ['extra_args' => 'atk4'], '/test/?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/index.php?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/index.php8?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/index?extra_args=atk4&query_args=atk4&page_args=atk4'],
            [['test', 'page_args' => 'atk4'], true, ['extra_args' => 'atk4'], '/', '/index.php?extra_args=atk4', '/default.php8?extra_args=atk4', '/?extra_args=atk4'],
            [['test', 'page_args' => 'atk4'], true, ['extra_args' => 'atk4'], '/request-url', '/request-url.php?extra_args=atk4', '/request-url.php8?extra_args=atk4', '/request-url?extra_args=atk4'],
            [['test', 'page_args' => 'atk4'], true, ['extra_args' => 'atk4'], '/request-url/', '/request-url/index.php?extra_args=atk4', '/request-url/default.php8?extra_args=atk4', '/request-url/?extra_args=atk4'],
            [['test/index', 'page_args' => 'atk4'], true, ['extra_args' => 'atk4'], '/test/', '/test/index.php?extra_args=atk4', '/test/default.php8?extra_args=atk4', '/test/?extra_args=atk4'],
            [['test', 'page_args' => 'atk4', 'check_unset_page' => false], false, ['extra_args' => 'atk4'], '/?extra_args=atk4&query_args=atk4&page_args=atk4', 'test.php?extra_args=atk4&query_args=atk4&page_args=atk4', 'test.php8?extra_args=atk4&query_args=atk4&page_args=atk4', 'test?extra_args=atk4&query_args=atk4&page_args=atk4'],
        ];
    }

    /**
     * @dataProvider provideUrlBuildingCases
     *
     * @param string|array<0|string, string|int|false> $page                URL as string or array with page name as first element and other GET arguments
     * @param bool                                     $useRequestUrl       Simply return $_SERVER['REQUEST_URI'] if needed
     * @param array<string, string>                    $extraRequestUrlArgs Additional URL arguments, deleting sticky can delete them
     */
    public function testUrlBuilding($page, bool $useRequestUrl, array $extraRequestUrlArgs, string $requestUrl, string $exceptedStd, string $exceptedCustom, string $exceptedRouting): void
    {
        $factory = new Psr17Factory();
        $request = $factory->createServerRequest('GET', 'http://127.0.0.1' . $requestUrl);

        $stickyGetArguments = [
            '__atk_json' => false,
            '__atk_tab' => false,
        ];

        foreach ($request->getQueryParams() as $key => $value) {
            $stickyGetArguments[$key] = $value;
        }

        $app = new App([
            'request' => $request,
            'stickyGetArguments' => $stickyGetArguments,
            'catchExceptions' => false,
            'alwaysRun' => false,
        ]);
        self::assertSame($exceptedStd, $app->url($page, $useRequestUrl, $extraRequestUrlArgs), 'App::url test error case: standard (from: ' . $requestUrl . ' and $useRequestUrl=' . (int) $useRequestUrl . ')');
        self::assertSame($exceptedStd, $app->jsUrl($page, $useRequestUrl, $extraRequestUrlArgs), 'App::jsUrl test error case: standard (from: ' . $requestUrl . ' and $useRequestUrl=' . (int) $useRequestUrl . ')');

        $app = new App([
            'request' => $request,
            'stickyGetArguments' => $stickyGetArguments,
            'urlBuildingIndexPage' => 'default',
            'urlBuildingExt' => '.php8',
            'catchExceptions' => false,
            'alwaysRun' => false,
        ]);
        self::assertSame($exceptedCustom, $app->url($page, $useRequestUrl, $extraRequestUrlArgs), 'App::url test error case: custom page/ext (from: ' . $requestUrl . ' and $useRequestUrl=' . (int) $useRequestUrl . ')');
        self::assertSame($exceptedCustom, $app->jsUrl($page, $useRequestUrl, $extraRequestUrlArgs), 'App::jsUrl test error case: custom page/ext (from: ' . $requestUrl . ' and $useRequestUrl=' . (int) $useRequestUrl . ')');

        $app = new App([
            'request' => $request,
            'stickyGetArguments' => $stickyGetArguments,
            'urlBuildingIndexPage' => '',
            'urlBuildingExt' => '',
            'catchExceptions' => false,
            'alwaysRun' => false,
        ]);
        self::assertSame($exceptedRouting, $app->url($page, $useRequestUrl, $extraRequestUrlArgs), 'App::url test error case: routing (from: ' . $requestUrl . ' and $useRequestUrl=' . (int) $useRequestUrl . ')');
        self::assertSame($exceptedRouting, $app->jsUrl($page, $useRequestUrl, $extraRequestUrlArgs), 'App::jsUrl test error case: routing (from: ' . $requestUrl . ' and $useRequestUrl=' . (int) $useRequestUrl . ')');
    }
}
