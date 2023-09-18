<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Callback;
use Atk4\Ui\CallbackLater;
use Atk4\Ui\Layout;
use Atk4\Ui\View;
use Atk4\Ui\VirtualPage;
use Psr\Http\Message\ServerRequestInterface;

class AppMock extends App
{
    /**
     * Overridden to allow multiple App::run() calls, prevent sending headers when headers are already sent.
     */
    protected function outputResponse(string $data): void
    {
        echo $data;
    }
}

class CallbackTest extends TestCase
{
    use CreateAppTrait;

    /** @var string */
    private $htmlDoctypeRegex = '~^<!DOCTYPE~';

    /** @var App */
    protected $app;

    protected function setupApp(): void
    {
        $this->app = $this->createApp([AppMock::class]);
        $this->app->initLayout([Layout\Centered::class]);
    }

    private function replaceAppRequest(App $app, ServerRequestInterface $request): void
    {
        $requestProperty = new \ReflectionProperty(App::class, 'request');
        $requestProperty->setAccessible(true);
        $requestProperty->setValue($app, $request);

        $this->setGlobalsFromRequest($request);
    }

    protected function simulateCallbackTriggering(Callback $cb): void
    {
        $request = $this->app->getRequest();
        $request = $this->triggerCallback($request, $cb);

        // TODO FormTest does not need this hack
        $request = $request->withQueryParams(array_diff_key($request->getQueryParams(), [Callback::URL_QUERY_TARGET => true]));

        $this->replaceAppRequest($this->app, $request);
    }

    public function testCallback(): void
    {
        $this->setupApp();

        $cb = Callback::addTo($this->app);

        $this->simulateCallbackTriggering($cb);

        $var = null;
        $cb->set(static function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        self::assertSame(34, $var);
        self::assertSame('1', $cb->getTriggeredValue());
    }

    public function testCallbackTrigger(): void
    {
        $this->setupApp();

        $cb = Callback::addTo($this->app);
        self::assertSame($this->app->layout->name . '_' . $cb->shortName, $cb->getUrlTrigger());

        $cb = Callback::addTo($this->app, ['urlTrigger' => 'test']);
        self::assertSame('test', $cb->getUrlTrigger());
    }

    public function testViewUrlCallback(): void
    {
        $this->setupApp();

        $cbApp = Callback::addTo($this->app, ['urlTrigger' => 'aa']);
        $v1 = View::addTo($this->app);
        $cb = Callback::addTo($v1, ['urlTrigger' => 'bb']);

        $this->simulateCallbackTriggering($cbApp);
        $this->simulateCallbackTriggering($cb);

        $expectedUrlCbApp = '/index.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'aa=callback&' . Callback::URL_QUERY_TARGET . '=aa';
        $expectedUrlCb = '/index.php?' . /* Callback::URL_QUERY_TRIGGER_PREFIX . 'aa=1&' . */ Callback::URL_QUERY_TRIGGER_PREFIX . 'bb=callback&' . Callback::URL_QUERY_TARGET . '=bb';
        self::assertSame($expectedUrlCbApp, $cbApp->getUrl());
        self::assertSame($expectedUrlCb, $cb->getUrl());

        // URL must remain the same when urlTrigger is set but name is changed
        $cbApp->name = 'aax';
        $cb->name = 'bbx';
        self::assertSame($expectedUrlCbApp, $cbApp->getUrl());
        self::assertSame($expectedUrlCb, $cb->getUrl());

        $var = null;
        $cb->set(static function (int $x) use (&$var, $v1) {
            $v3 = View::addTo($v1);
            self::assertSame('test.php', $v3->url(['test']));
            $var = $x;
        }, [34]);

        $v2 = View::addTo($v1);
        $v2->stickyGet('g1', '1');

        self::assertSame(34, $var);
        self::assertSame('test.php?g1=1', $v2->url(['test']));
    }

    public function testCallbackNotFiring(): void
    {
        $this->setupApp();

        $cb = Callback::addTo($this->app);

        // do NOT simulate triggering in this test

        $var = null;
        $cb->set(static function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        self::assertNull($var);
    }

    public function testCallbackLater(): void
    {
        $this->setupApp();

        $cb = CallbackLater::addTo($this->app);

        $this->simulateCallbackTriggering($cb);

        $var = null;
        $cb->set(static function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        self::assertNull($var);

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();

        self::assertSame(34, $var);
    }

    public function testCallbackLaterNested(): void
    {
        $this->setupApp();

        $cb = CallbackLater::addTo($this->app);

        $this->simulateCallbackTriggering($cb);

        $var = null;
        $cb->set(function (int $x) use (&$var) {
            $cb2 = CallbackLater::addTo($this->app);

            $this->simulateCallbackTriggering($cb2);

            $cb2->set(static function (int $y) use (&$var) {
                $var = $y;
            }, [$x]);
        }, [34]);

        self::assertNull($var);

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();

        self::assertSame(34, $var);
    }

    public function testCallbackLaterNotFiring(): void
    {
        $this->setupApp();

        $cb = CallbackLater::addTo($this->app);

        // don't simulate triggering
        $var = null;
        $cb->set(static function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        self::assertNull($var);

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();

        self::assertNull($var); // @phpstan-ignore-line
    }

    public function testVirtualPage(): void
    {
        $this->setupApp();

        $vp = VirtualPage::addTo($this->app);

        $this->simulateCallbackTriggering($vp->cb);

        $var = null;
        $vp->set(static function (VirtualPage $p) use (&$var) {
            $var = 25;
        });

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();
        self::assertSame(25, $var);
    }

    public function testVirtualPageCustomTrigger(): void
    {
        $this->setupApp();

        $vp = VirtualPage::addTo($this->app, ['urlTrigger' => 'bah']);

        $this->simulateCallbackTriggering($vp->cb);

        $var = null;
        $vp->set(static function (VirtualPage $p) use (&$var) {
            $var = 25;
        });

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();
        self::assertSame(25, $var);
    }

    /** @var int */
    private $varPull230;

    public function testPull230(): void
    {
        $this->setupApp();

        $vp = VirtualPage::addTo($this->app);

        $this->simulateCallbackTriggering($vp->cb);

        $vp->set(function () {
            $this->varPull230 = 26;
        });

        $this->expectOutputRegex('~^..DOCTYPE~');
        $this->app->run();
        self::assertSame(26, $this->varPull230);
    }
}
