<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Callback;

class AppMock extends \Atk4\Ui\App
{
    public $terminated = false;

    public function terminate($output = '', array $headers = []): void
    {
        $this->terminate = true;
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
        $this->app = new AppMock(['always_run' => false, 'catch_exceptions' => false]);
        $this->app->initLayout([\Atk4\Ui\Layout\Centered::class]);
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
    }

    public function testCallback(): void
    {
        $var = null;

        $cb = \Atk4\Ui\Callback::addTo($this->app);

        // simulate triggering
        $_GET[Callback::URL_TRIGGER_PREFIX . $cb->name] = '1';

        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertSame(34, $var);
        $this->assertSame('1', $cb->getTriggeredValue());
    }

    public function testCallbackTrigger(): void
    {
        $cb = \Atk4\Ui\Callback::addTo($this->app);
        $this->assertSame($this->app->layout->name . '_' . $cb->short_name, $cb->getUrlTrigger());

        $cb = Callback::addTo($this->app, ['urlTrigger' => 'test']);
        $this->assertSame('test', $cb->getUrlTrigger());
    }

    public function testViewUrlCallback(): void
    {
        $var = null;

        $v1 = \Atk4\Ui\View::addTo($this->app);
        $cb = \Atk4\Ui\Callback::addTo($v1);

        // simulate triggering
        $_GET[Callback::URL_TRIGGER_PREFIX . $cb->name] = '1';

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
        $var = null;

        $cb = \Atk4\Ui\Callback::addTo($this->app);

        // don't simulate triggering
        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertNull($var);
    }

    public function testCallbackLater(): void
    {
        $var = null;

        $cb = \Atk4\Ui\CallbackLater::addTo($this->app);

        // simulate triggering
        $_GET[Callback::URL_TRIGGER_PREFIX . $cb->name] = '1';

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
        $var = null;

        $cb = \Atk4\Ui\CallbackLater::addTo($this->app);

        // simulate triggering
        $_GET[Callback::URL_TRIGGER_PREFIX . $cb->name] = '1';
        $_GET[Callback::URL_TRIGGER_PREFIX . $cb->name . '_2'] = '1';

        $app = $this->app;
        $cb->set(function ($x) use (&$var, $app, &$cbname) {
            $cb2 = \Atk4\Ui\CallbackLater::addTo($app);
            $cbname = $cb2->name;
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
        $var = null;

        $cb = \Atk4\Ui\CallbackLater::addTo($this->app);

        // don't simulate triggering
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
        $var = null;

        $vp = \Atk4\Ui\VirtualPage::addTo($this->app);

        // simulate triggering
        $_GET[Callback::URL_TRIGGER_PREFIX . $vp->name] = '1';

        $vp->set(function ($p) use (&$var) {
            $var = 25;
        });

        $this->expectOutputRegex('/^..DOCTYPE/');
        $this->app->run();
        $this->assertSame(25, $var);
    }

    public function testVirtualPageCustomTrigger(): void
    {
        $var = null;

        $vp = \Atk4\Ui\VirtualPage::addTo($this->app, ['urlTrigger' => 'bah']);

        // simulate triggering
        $_GET[Callback::URL_TRIGGER_PREFIX . 'bah'] = '1';

        $vp->set(function ($p) use (&$var) {
            $var = 25;
        });

        $this->expectOutputRegex('/^..DOCTYPE/');
        $this->app->run();
        $this->assertSame(25, $var);
    }

    public $var;

    public function callPull230()
    {
        $this->var = 26;
    }

    public function testPull230(): void
    {
        $var = null;

        $vp = \Atk4\Ui\VirtualPage::addTo($this->app);

        // simulate triggering
        $_GET[Callback::URL_TRIGGER_PREFIX . $vp->name] = '1';

        $vp->set(\Closure::fromCallable([$this, 'callPull230']));

        $this->expectOutputRegex('/^..DOCTYPE/');
        $this->app->run();
        $this->assertSame(26, $this->var);
    }
}
