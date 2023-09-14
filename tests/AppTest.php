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
        return [
            // simple cases
            [[], [], '/', '/index.php', '/default.php8', '/'],
            [[], [], '/test/', '/test/index.php', '/test/default.php8', '/test/'],

            // simple cases with query args in request
            [[], [], '/?test=atk4', '/index.php?test=atk4', '/default.php8?test=atk4', '/?test=atk4'],
            [[], [], '/test/?test=atk4', '/test/index.php?test=atk4', '/test/default.php8?test=atk4', '/test/?test=atk4'],

            // simple cases with extra query args in request
            [[], ['extra_args' => 'atk4'], '/?test=atk4', '/index.php?extra_args=atk4&test=atk4', '/default.php8?extra_args=atk4&test=atk4', '/?extra_args=atk4&test=atk4'],
            [[], ['extra_args' => 'atk4'], '/test/?test=atk4', '/test/index.php?extra_args=atk4&test=atk4', '/test/default.php8?extra_args=atk4&test=atk4', '/test/?extra_args=atk4&test=atk4'],

            // simple cases with page as string
            ['test', [], '/', 'test.php', 'test.php8', 'test'],
            ['test/test/a', [], '/', 'test/test/a.php', 'test/test/a.php8', 'test/test/a'],
            ['test/index', [], '/', 'test/index.php', 'test/index.php8', 'test/index'],
            ['test/index', [], '/test/', 'test/index.php', 'test/index.php8', 'test/index'],
            // failing on linux, failing on Windows for different/dirname reason ['/index.php', [], '/', '/index.php', '/default.php8', '/'],
            // failing on Windows for dirname reason ['/request-url', [], '/request-url', '/request-url.php', '/request-url.php8', '/request-url'],
            ['/request-url/', [], '/request-url/', '/request-url/index.php', '/request-url/default.php8', '/request-url/'],
            ['/test/', [], '/test/', '/test/index.php', '/test/default.php8', '/test/'],

            // simple cases with page as array with 0 => string
            [['test'], [], '/', 'test.php', 'test.php8', 'test'],
            [['test/test/a'], [], '/', 'test/test/a.php', 'test/test/a.php8', 'test/test/a'],
            [['test/index'], [], '/test/', 'test/index.php', 'test/index.php8', 'test/index'],

            // query args in page cases
            [['test', 'extra_args' => 'atk4'], [], '/', 'test.php?extra_args=atk4', 'test.php8?extra_args=atk4', 'test?extra_args=atk4'],
            [['test/test/a', 'extra_args' => 'atk4'], [], '/', 'test/test/a.php?extra_args=atk4', 'test/test/a.php8?extra_args=atk4', 'test/test/a?extra_args=atk4'],
            [['test/index', 'extra_args' => 'atk4'], [], '/test/', 'test/index.php?extra_args=atk4', 'test/index.php8?extra_args=atk4', 'test/index?extra_args=atk4'],

            // extra query args cases
            [['test'], ['extra_args' => 'atk4'], '/', 'test.php?extra_args=atk4', 'test.php8?extra_args=atk4', 'test?extra_args=atk4'],
            [['test/test/a'], ['extra_args' => 'atk4'], '/', 'test/test/a.php?extra_args=atk4', 'test/test/a.php8?extra_args=atk4', 'test/test/a?extra_args=atk4'],
            [['test/index'], ['extra_args' => 'atk4'], '/test/', 'test/index.php?extra_args=atk4', 'test/index.php8?extra_args=atk4', 'test/index?extra_args=atk4'],

            // query args in page cases and query args in request cases and extra query args cases
            [['test', 'page_args' => 'atk4'], ['extra_args' => 'atk4'], '/?extra_args=atk4&query_args=atk4&page_args=atk4', 'test.php?extra_args=atk4&query_args=atk4&page_args=atk4', 'test.php8?extra_args=atk4&query_args=atk4&page_args=atk4', 'test?extra_args=atk4&query_args=atk4&page_args=atk4'],
            [['test/test/a', 'page_args' => 'atk4'], ['extra_args' => 'atk4'], '/?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/test/a.php?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/test/a.php8?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/test/a?extra_args=atk4&query_args=atk4&page_args=atk4'],
            [['test/index', 'page_args' => 'atk4'], ['extra_args' => 'atk4'], '/test/?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/index.php?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/index.php8?extra_args=atk4&query_args=atk4&page_args=atk4', 'test/index?extra_args=atk4&query_args=atk4&page_args=atk4'],
        ];
    }

    /**
     * @dataProvider provideUrlBuildingCases
     *
     * @param string|array<0|string, string|int|false> $page                URL as string or array with page name as first element and other GET arguments
     * @param array<string, string>                    $extraRequestUrlArgs Additional URL arguments, deleting sticky can delete them
     */
    public function testUrlBuilding($page, array $extraRequestUrlArgs, string $requestUrl, string $exceptedStd, string $exceptedCustom, string $exceptedRouting): void
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

        $app = $this->createApp([
            'request' => $request,
            'stickyGetArguments' => $stickyGetArguments,
            'urlBuildingIndexPage' => 'default',
            'urlBuildingExt' => '.php8',
        ]);

        self::assertSame($exceptedCustom, $app->url($page, $extraRequestUrlArgs));
        self::assertSame($exceptedCustom, $app->jsUrl($page, $extraRequestUrlArgs));

        $app = $this->createApp([
            'request' => $request,
            'stickyGetArguments' => $stickyGetArguments,
            'urlBuildingIndexPage' => '',
            'urlBuildingExt' => '',
        ]);
        self::assertSame($exceptedRouting, $app->url($page, $extraRequestUrlArgs));
        self::assertSame($exceptedRouting, $app->jsUrl($page, $extraRequestUrlArgs));
    }
}
