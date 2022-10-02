<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\AbstractView;
use Atk4\Ui\App;
use Atk4\Ui\Callback;
use Atk4\Ui\CallbackLater;
use Atk4\Ui\Layout;
use Atk4\Ui\View;
use Atk4\Ui\VirtualPage;

class AppMock extends App
{
    /**
     * Overriden to allow multiple App::run() calls, prevent sending headers when headers are already sent.
     */
    protected function outputResponse(string $data, array $headers): void
    {
        echo $data;
    }
}

class CallbackTest extends TestCase
{
    /** @var string */
    private $htmlDoctypeRegex = '~^<!DOCTYPE~';

    /** @var App */
    public $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new AppMock(['alwaysRun' => false, 'catchExceptions' => false]);
        $this->app->initLayout([Layout\Centered::class]);
    }

    protected function tearDown(): void
    {
        unset($_GET);
        unset($_POST);

        parent::tearDown();
    }

    /**
     * @param Callback|VirtualPage $cb
     */
    protected function simulateCallbackTriggering(AbstractView $cb): void
    {
        $_GET[Callback::URL_QUERY_TRIGGER_PREFIX . $cb->getUrlTrigger()] = '1';
    }

    public function testCallback(): void
    {
        $cb = Callback::addTo($this->app);

        $this->simulateCallbackTriggering($cb);

        $var = null;
        $cb->set(function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        static::assertSame(34, $var);
        static::assertSame('1', $cb->getTriggeredValue());
    }

    public function testCallbackTrigger(): void
    {
        $cb = Callback::addTo($this->app);
        static::assertSame($this->app->layout->name . '_' . $cb->shortName, $cb->getUrlTrigger());

        $cb = Callback::addTo($this->app, ['urlTrigger' => 'test']);
        static::assertSame('test', $cb->getUrlTrigger());
    }

    public function testViewUrlCallback(): void
    {
        $cbApp = Callback::addTo($this->app, ['urlTrigger' => 'aa']);
        $v1 = View::addTo($this->app);
        $cb = Callback::addTo($v1, ['urlTrigger' => 'bb']);

        $this->simulateCallbackTriggering($cbApp);
        $this->simulateCallbackTriggering($cb);

        $expectedUrlCbApp = '?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'aa=callback&' . Callback::URL_QUERY_TARGET . '=aa';
        $expectedUrlCb = '?' . /* Callback::URL_QUERY_TRIGGER_PREFIX . 'aa=1&' . */ Callback::URL_QUERY_TRIGGER_PREFIX . 'bb=callback&' . Callback::URL_QUERY_TARGET . '=bb';
        static::assertSame($expectedUrlCbApp, $cbApp->getUrl());
        static::assertSame($expectedUrlCb, $cb->getUrl());

        // URL must remain the same when urlTrigger is set but name is changed
        $cbApp->name = 'aax';
        $cb->name = 'bbx';
        static::assertSame($expectedUrlCbApp, $cbApp->getUrl());
        static::assertSame($expectedUrlCb, $cb->getUrl());

        $var = null;
        $cb->set(function (int $x) use (&$var, $v1) {
            $v3 = View::addTo($v1);
            static::assertSame('test.php', $v3->url(['test']));
            $var = $x;
        }, [34]);

        $v2 = View::addTo($v1);
        $v2->stickyGet('g1', '1');

        static::assertSame(34, $var);
        static::assertSame('test.php?g1=1', $v2->url(['test']));
    }

    public function testCallbackNotFiring(): void
    {
        $cb = Callback::addTo($this->app);

        // do NOT simulate triggering in this test

        $var = null;
        $cb->set(function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        static::assertNull($var);
    }

    public function testCallbackLater(): void
    {
        $cb = CallbackLater::addTo($this->app);

        $this->simulateCallbackTriggering($cb);

        $var = null;
        $cb->set(function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        static::assertNull($var);

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();

        static::assertSame(34, $var);
    }

    public function testCallbackLaterNested(): void
    {
        $cb = CallbackLater::addTo($this->app);

        $this->simulateCallbackTriggering($cb);

        $var = null;
        $cb->set(function (int $x) use (&$var) {
            $cb2 = CallbackLater::addTo($this->app);

            $this->simulateCallbackTriggering($cb2);

            $cb2->set(function (int $y) use (&$var) {
                $var = $y;
            }, [$x]);
        }, [34]);

        static::assertNull($var);

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();

        static::assertSame(34, $var);
    }

    public function testCallbackLaterNotFiring(): void
    {
        $cb = CallbackLater::addTo($this->app);

        // don't simulate triggering
        $var = null;
        $cb->set(function (int $x) use (&$var) {
            $var = $x;
        }, [34]);

        static::assertNull($var);

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();

        static::assertNull($var);
    }

    public function testVirtualPage(): void
    {
        $vp = VirtualPage::addTo($this->app);

        $this->simulateCallbackTriggering($vp);

        $var = null;
        $vp->set(function (VirtualPage $p) use (&$var) {
            $var = 25;
        });

        $this->expectOutputRegex('/^..DOCTYPE/');
        $this->app->run();
        static::assertSame(25, $var);
    }

    public function testVirtualPageCustomTrigger(): void
    {
        $vp = VirtualPage::addTo($this->app, ['urlTrigger' => 'bah']);

        $this->simulateCallbackTriggering($vp);

        $var = null;
        $vp->set(function (VirtualPage $p) use (&$var) {
            $var = 25;
        });

        $this->expectOutputRegex('/^..DOCTYPE/');
        $this->app->run();
        static::assertSame(25, $var);
    }

    /** @var int */
    private $varPull230;

    public function testPull230(): void
    {
        $vp = VirtualPage::addTo($this->app);

        $this->simulateCallbackTriggering($vp);

        $vp->set(function () {
            $this->varPull230 = 26;
        });

        $this->expectOutputRegex('/^..DOCTYPE/');
        $this->app->run();
        static::assertSame(26, $this->varPull230);
    }
}
