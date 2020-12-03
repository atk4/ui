<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;

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

class CallbackTest extends AtkPhpunit\TestCase
{
    /** @var string */
    private $htmlDoctypeRegex = '~^<!DOCTYPE~';

    /** @var \Atk4\Ui\App */
    public $app;

    protected function setUp(): void
    {
        $this->app = new AppMock(['always_run' => false, 'catch_exceptions' => false]);
        $this->app->initLayout([\Atk4\Ui\Layout\Centered::class]);

        // reset var, between tests
        $_GET = [];
        $_POST = [];
    }

    public function testCallback()
    {
        $var = null;

        $cb = \Atk4\Ui\Callback::addTo($this->app);

        // simulate triggering
        $_GET[$cb->name] = '1';

        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertSame(34, $var);
    }

    public function testCallbackNotFiring()
    {
        $var = null;

        $cb = \Atk4\Ui\Callback::addTo($this->app);

        // don't simulate triggering
        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertNull($var);
    }

    public function testCallbackLater()
    {
        $var = null;

        $cb = \Atk4\Ui\CallbackLater::addTo($this->app);

        // simulate triggering
        $_GET[$cb->name] = '1';

        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertNull($var);

        $this->expectOutputRegex($this->htmlDoctypeRegex);
        $this->app->run();

        $this->assertSame(34, $var);
    }

    public function testCallbackLaterNested()
    {
        $var = null;

        $cb = \Atk4\Ui\CallbackLater::addTo($this->app);

        // simulate triggering
        $_GET[$cb->name] = '1';
        $_GET[$cb->name . '_2'] = '1';

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

    public function testCallbackLaterNotFiring()
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

    public function testVirtualPage()
    {
        $var = null;

        $vp = \Atk4\Ui\VirtualPage::addTo($this->app);
        // simulate triggering

        $vp->set(function ($p) use (&$var) {
            $var = 25;
        });

        $_GET[$vp->name] = '1';

        $this->expectOutputRegex('/^..DOCTYPE/');
        $this->app->run();
        $this->assertSame(25, $var);
    }

    public function testVirtualPageCustomTrigger()
    {
        $var = null;

        $vp = \Atk4\Ui\VirtualPage::addTo($this->app, ['urlTrigger' => 'bah']);
        $vp->set(function ($p) use (&$var) {
            $var = 25;
        });

        // simulate triggering
        $_GET['bah'] = '1';

        $this->expectOutputRegex('/^..DOCTYPE/');
        $this->app->run();
        $this->assertSame(25, $var);
    }

    public $var;

    public function callPull230()
    {
        $this->var = 26;
    }

    public function testPull230()
    {
        $var = null;

        $vp = \Atk4\Ui\VirtualPage::addTo($this->app);
        $vp->set([$this, 'callPull230']);

        // simulate triggering
        $_GET[$vp->name] = '1';

        $this->expectOutputRegex('/^..DOCTYPE/');
        $this->app->run();
        $this->assertSame(26, $this->var);
    }
}
