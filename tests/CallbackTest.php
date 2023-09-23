<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Callback;
use Atk4\Ui\CallbackLater;
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
    use CreateAppTrait {
        createApp as private _createApp;
        triggerCallback as private _triggerCallback;
    }

    /** @var string */
    private $regexHtmlDoctype = '~^<!DOCTYPE html>\s*<html~';

    protected function createApp(array $seed = []): App
    {
        if (!isset($seed[0])) {
            $seed[0] = AppMock::class;
        }

        return $this->_createApp($seed);
    }

    protected function triggerCallback(ServerRequestInterface $request, Callback $cb, string $triggerValue = '1'): ServerRequestInterface
    {
        $request = $this->_triggerCallback($request, $cb, $triggerValue);

        // TODO FormTest does not need this hack
        $request = $request->withQueryParams(array_diff_key($request->getQueryParams(), [Callback::URL_QUERY_TARGET => true]));

        return $request;
    }

    public function testCallback(): void
    {
        $cb = $this->simulateViewCallback(function (ServerRequestInterface $request) {
            $app = $this->createApp(['request' => $request]);
            $cb = Callback::addTo($app);

            return $cb;
        }, function (Callback $cb) {
            return $this->triggerCallback($cb->getApp()->getRequest(), $cb);
        });

        $var = null;
        $cb->set(static function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        self::assertSame(34, $var);
        self::assertSame('1', $cb->getTriggeredValue());
    }

    public function testCallbackUrlTrigger(): void
    {
        $app = $this->createApp();
        $cb = Callback::addTo($app);
        self::assertSame($app->layout->name . '_' . $cb->shortName, $cb->getUrlTrigger());

        $cb = Callback::addTo($app, ['urlTrigger' => 'test']);
        self::assertSame('test', $cb->getUrlTrigger());
    }

    public function testViewUrlCallback(): void
    {
        $v1 = null;
        $cb = null;
        $cbApp = $this->simulateViewCallback(function (ServerRequestInterface $request) use (&$v1, &$cb) {
            $app = $this->createApp(['request' => $request]);
            $cbApp = Callback::addTo($app, ['urlTrigger' => 'aa']);
            $v1 = View::addTo($app);
            $cb = Callback::addTo($v1, ['urlTrigger' => 'bb']);

            return $cbApp;
        }, function (Callback $cbApp) use (&$cb) {
            $request = $this->triggerCallback($cbApp->getApp()->getRequest(), $cbApp);
            $request = $this->triggerCallback($request, $cb);

            return $request;
        });

        $expectedUrlCbApp = '/index.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'aa=callback&' . Callback::URL_QUERY_TARGET . '=aa';
        $expectedUrlCb = '/index.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'aa=1&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'bb=callback&' . Callback::URL_QUERY_TARGET . '=bb';
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
            self::assertSame('test.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'aa=1&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'bb=1', $v3->url(['test']));
            $var = $x;
        }, [34]);

        $v2 = View::addTo($v1);
        $v2->stickyGet('g1', '1');

        self::assertSame(34, $var);
        self::assertSame('test.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'aa=1&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'bb=1&g1=1', $v2->url(['test']));
    }

    public function testCallbackNotFiring(): void
    {
        $app = $this->createApp();
        $cb = Callback::addTo($app);

        // do NOT simulate triggering in this test

        $var = null;
        $cb->set(static function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        self::assertNull($var);
    }

    public function testCallbackLater(): void
    {
        $cb = $this->simulateViewCallback(function (ServerRequestInterface $request) {
            $app = $this->createApp(['request' => $request]);
            $cb = CallbackLater::addTo($app);

            return $cb;
        }, function (Callback $cb) {
            return $this->triggerCallback($cb->getApp()->getRequest(), $cb);
        });

        $var = null;
        $cb->set(static function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        self::assertNull($var);

        $this->expectOutputRegex($this->regexHtmlDoctype);
        $cb->getApp()->run();
        self::assertSame(34, $var);
    }

    public function testCallbackLaterNested(): void
    {
        $createCb2Fx = static function (Callback $cb) {
            return CallbackLater::addTo($cb->getApp());
        };

        $var = null;
        $cb = $this->simulateViewCallback(function (ServerRequestInterface $request) use ($createCb2Fx, &$var) {
            $app = $this->createApp(['request' => $request]);
            $cb = CallbackLater::addTo($app);

            $cb->set(static function (int $x) use ($createCb2Fx, &$var, $cb) {
                $cb2 = $createCb2Fx($cb);

                $cb2->set(static function (int $y) use (&$var) {
                    $var = $y;
                }, [$x + 100]);
            }, [34]);

            return $cb;
        }, function (Callback $cb) use ($createCb2Fx) {
            $cb2 = $createCb2Fx($cb);

            $request = $this->triggerCallback($cb->getApp()->getRequest(), $cb);
            $request = $this->triggerCallback($request, $cb2);

            return $request;
        });

        self::assertNull($var);

        $this->expectOutputRegex($this->regexHtmlDoctype);
        $cb->getApp()->run();
        self::assertSame(134, $var);
    }

    public function testCallbackLaterNotFiring(): void
    {
        $app = $this->createApp();
        $cb = CallbackLater::addTo($app);

        // do NOT simulate triggering in this test

        $var = null;
        $cb->set(static function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        self::assertNull($var);

        $this->expectOutputRegex($this->regexHtmlDoctype);
        $app->run();
        self::assertNull($var); // @phpstan-ignore-line
    }

    public function testVirtualPage(): void
    {
        $vp = $this->simulateViewCallback(function (ServerRequestInterface $request) {
            $app = $this->createApp(['request' => $request]);
            $vp = VirtualPage::addTo($app);

            return $vp;
        }, function (VirtualPage $vp) {
            return $this->triggerCallback($vp->getApp()->getRequest(), $vp->cb);
        });

        $var = null;
        $vp->set(static function (VirtualPage $p) use (&$var) {
            $var = 25;
        });

        $this->expectOutputRegex($this->regexHtmlDoctype);
        $vp->getApp()->run();
        self::assertSame(25, $var);
    }

    public function testVirtualPageCustomUrlTrigger(): void
    {
        $vp = $this->simulateViewCallback(function (ServerRequestInterface $request) {
            $app = $this->createApp(['request' => $request]);
            $vp = VirtualPage::addTo($app, ['urlTrigger' => 'bah']);

            return $vp;
        }, function (VirtualPage $vp) {
            return $this->triggerCallback($vp->getApp()->getRequest(), $vp->cb);
        });

        $var = null;
        $vp->set(static function (VirtualPage $p) use (&$var) {
            $var = 25;
        });

        $this->expectOutputRegex($this->regexHtmlDoctype);
        $vp->getApp()->run();
        self::assertSame(25, $var);
    }
}
