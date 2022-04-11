<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\AbstractView;
use Atk4\Ui\Callback;
use Atk4\Ui\VirtualPage;
use Mvorisek\Atk4\Hintable\Phpstan\PhpstanUtil;

class AppMock extends \Atk4\Ui\App
{
    /** @var bool */
    public $terminated = false;

    public function terminate($output = '', array $headers = []): void
    {
        $this->terminated = true;

        PhpstanUtil::fakeNeverReturn();
    }

    /**
     * Overrided to allow multiple App::run() calls, prevent sending headers when headers are already sent.
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

    /** @var \Atk4\Ui\App */
    public $app;

    protected function setUp(): void
    {
        $this->app = new AppMock(['alwaysRun' => false, 'catchExceptions' => false]);
        $this->app->initLayout([\Atk4\Ui\Layout\Centered::class]);
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
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
        $cb = \Atk4\Ui\Callback::addTo($this->app);

        $this->simulateCallbackTriggering($cb);

        $var = null;
        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertSame(34, $var);
        $this->assertSame('1', $cb->getTriggeredValue());
    }

    public function testCallbackTrigger(): void
    {
        $cb = \Atk4\Ui\Callback::addTo($this->app);
        $this->assertSame($this->app->layout->name . '_' . $cb->shortName, $cb->getUrlTrigger());

        $cb = Callback::addTo($this->app, ['urlTrigger' => 'test']);
        $this->assertSame('test', $cb->getUrlTrigger());
    }

    public function testViewUrlCallback(): void
    {
        $cbApp = \Atk4\Ui\Callback::addTo($this->app, ['urlTrigger' => 'aa']);
        $v1 = \Atk4\Ui\View::addTo($this->app);
        $cb = \Atk4\Ui\Callback::addTo($v1, ['urlTrigger' => 'bb']);

        $this->simulateCallbackTriggering($cbApp);
        $this->simulateCallbackTriggering($cb);

        $expectedUrlCbApp = '?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'aa=callback&' . Callback::URL_QUERY_TARGET . '=aa';
        $expectedUrlCb = '?' . /* Callback::URL_QUERY_TRIGGER_PREFIX . 'aa=1&' . */ Callback::URL_QUERY_TRIGGER_PREFIX . 'bb=callback&' . Callback::URL_QUERY_TARGET . '=bb';
        $this->assertSame($expectedUrlCbApp, $cbApp->getUrl());
        $this->assertSame($expectedUrlCb, $cb->getUrl());

        // URL must remain the same when urlTrigger is set but name is changed
        $cbApp->name = 'aax';
        $cb->name = 'bbx';
        $this->assertSame($expectedUrlCbApp, $cbApp->getUrl());
        $this->assertSame($expectedUrlCb, $cb->getUrl());

        $var = null;
        $cb->set(function ($x) use (&$var, $v1) {
            $v3 = \Atk4\Ui\View::addTo($v1);
            $this->assertSame('test.php', $v3->url(['test']));
            $var = $x;
        }, [34]);

        $v2 = \Atk4\Ui\View::addTo($v1);
        $v2->stickyGet('g1', '1');

        $this->assertSame(34, $var);
        $this->assertSame('test.php?g1=1', $v2->url(['test']));
    }

    public function testCallbackNotFiring(): void
    {
        $cb = \Atk4\Ui\Callback::addTo($this->app);

        // do NOT simulate triggering in this test

        $var = null;
        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertNull($var);
    }

    public function testCallbackLater(): void
    {
        $cb = \Atk4\Ui\CallbackLater::addTo($this->app);

        $this->simulateCallbackTriggering($cb);

        $var = null;
        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertNull($var);

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();

        $this->assertSame(34, $var);
    }

    public function testCallbackLaterNested(): void
    {
        $cb = \Atk4\Ui\CallbackLater::addTo($this->app);

        $this->simulateCallbackTriggering($cb);

        $var = null;
        $cb->set(function ($x) use (&$var) {
            $cb2 = \Atk4\Ui\CallbackLater::addTo($this->app);

            $this->simulateCallbackTriggering($cb2);

            $cb2->set(function ($y) use (&$var) {
                $var = $y;
            }, [$x]);
        }, [34]);

        $this->assertNull($var);

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();

        $this->assertSame(34, $var);
    }

    public function testCallbackLaterNotFiring(): void
    {
        $cb = \Atk4\Ui\CallbackLater::addTo($this->app);

        // don't simulate triggering
        $var = null;
        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertNull($var);

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();

        $this->assertNull($var);
    }

    public function testVirtualPage(): void
    {
        $vp = VirtualPage::addTo($this->app);

        $this->simulateCallbackTriggering($vp);

        $var = null;
        $vp->set(function ($p) use (&$var) {
            $var = 25;
        });

        $this->expectOutputRegex('/^..DOCTYPE/');
        $this->app->run();
        $this->assertSame(25, $var);
    }

    public function testVirtualPageCustomTrigger(): void
    {
        $vp = VirtualPage::addTo($this->app, ['urlTrigger' => 'bah']);

        $this->simulateCallbackTriggering($vp);

        $var = null;
        $vp->set(function ($p) use (&$var) {
            $var = 25;
        });

        $this->expectOutputRegex('/^..DOCTYPE/');
        $this->app->run();
        $this->assertSame(25, $var);
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
        $this->assertSame(26, $this->varPull230);
    }
}
