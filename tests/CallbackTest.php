<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;

class AppMock extends \atk4\ui\App
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
    /**
     * Test constructor.
     */
    private $regex = '/^..DOCTYPE/';

    public $app;

    public function setUp(): void
    {
        $this->app = new AppMock(['always_run' => false]);
        $this->app->initLayout('Centered');
    }

    public function testCallback()
    {
        $var = null;

        $app = $this->app;

        $cb = \atk4\ui\Callback::addTo($app);

        // simulate triggering
        $_GET[$cb->name] = true;

        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertEquals(34, $var);
    }

    public function testCallbackNotFiring()
    {
        $var = null;

        $app = $this->app;

        $cb = \atk4\ui\Callback::addTo($app);

        // don't simulate triggering
        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertEquals(null, $var);
    }

    public function testCallbackPOST()
    {
        $var = null;

        $app = $this->app;

        $cb = \atk4\ui\Callback::addTo($app, ['postTrigger' => 'go']);

        // simulate triggering
        $_POST['go'] = true;

        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertEquals(34, $var);
    }

    public function testCallbackLater()
    {
        $var = null;

        $app = $this->app;

        $cb = \atk4\ui\CallbackLater::addTo($app);

        // simulate triggering
        $_GET[$cb->name] = true;

        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertEquals(null, $var);

        $this->expectOutputRegex($this->regex);
        $app->run();

        $this->assertEquals(34, $var);
    }

    public function testCallbackLaterNested()
    {
        $var = null;

        $app = $this->app;

        $cb = \atk4\ui\CallbackLater::addTo($app);

        // simulate triggering
        $_GET[$cb->name] = true;
        $_GET[$cb->name . '_2'] = true;

        $cb->set(function ($x) use (&$var, $app, &$cbname) {
            $cb2 = \atk4\ui\CallbackLater::addTo($app);
            $cbname = $cb2->name;
            $cb2->set(function ($y) use (&$var) {
                $var = $y;
            }, [$x]);
        }, [34]);

        $this->assertEquals(null, $var);

        $this->expectOutputRegex($this->regex);
        $app->run();

        $this->assertEquals(34, $var);
    }

    public function testCallbackLaterNotFiring()
    {
        $var = null;

        $app = $this->app;

        $cb = \atk4\ui\CallbackLater::addTo($app);

        // don't simulate triggering
        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertEquals(null, $var);

        $this->expectOutputRegex($this->regex);
        $app->run();

        $this->assertEquals(null, $var);
    }

    public function testVirtualPage()
    {
        $var = null;

        $app = $this->app;

        $vp = \atk4\ui\VirtualPage::addTo($app);
        $vp->set(function ($p) use (&$var) {
            $var = 25;
        });

        // simulate triggering
        $_GET[$vp->name] = true;

        $this->expectOutputRegex('/^..DOCTYPE/');
        $app->run();
        $this->assertEquals(25, $var);
    }

    public function testVirtualPageCustomTrigger()
    {
        $var = null;

        $app = $this->app;

        $vp = \atk4\ui\VirtualPage::addTo($app, ['urlTrigger'=>'bah']);
        $vp->set(function ($p) use (&$var) {
            $var = 25;
        });

        // simulate triggering
        $_GET['bah'] = true;

        $this->expectOutputRegex('/^..DOCTYPE/');
        $app->run();
        $this->assertEquals(25, $var);
    }

    public $var = null;

    public function callPull230()
    {
        $this->var = 26;
    }

    public function testPull230()
    {
        $var = null;

        $app = $this->app;

        $vp = \atk4\ui\VirtualPage::addTo($app);
        $vp->set([$this, 'callPull230']);

        // simulate triggering
        $_GET[$vp->name] = true;

        $this->expectOutputRegex('/^..DOCTYPE/');
        $app->run();
        $this->assertEquals(26, $this->var);
    }
}
