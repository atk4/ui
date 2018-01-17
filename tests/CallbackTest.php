<?php

namespace atk4\ui\tests;

class AppMock extends \atk4\ui\App
{
    public $terminated = false;

    public function terminate($output = null)
    {
        $this->terminate = true;
    }
}

class CallbackTest extends \atk4\core\PHPUnit_AgileTestCase
{
    /**
     * Test constructor.
     */
    private $regex = '/^..DOCTYPE/';

    public $app;

    public function setUp()
    {
        $this->app = new AppMock(['always_run' => false]);
        $this->app->initLayout('Centered');
    }

    public function testCallback()
    {
        $var = null;

        $app = $this->app;

        $cb = $app->add('Callback');

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

        $cb = $app->add('Callback');

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

        $cb = $app->add(['Callback', 'postTrigger' => 'go']);

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

        $cb = $app->add('CallbackLater');

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

    public function testCallbackLaterNotFiring()
    {
        $var = null;

        $app = $this->app;

        $cb = $app->add('CallbackLater');

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

        $vp = $app->add('VirtualPage');
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

        $vp = $app->add(['VirtualPage', 'urlTrigger'=>'bah']);
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

        $vp = $app->add('VirtualPage');
        $vp->set([$this, 'callPull230']);

        // simulate triggering
        $_GET[$vp->name] = true;

        $this->expectOutputRegex('/^..DOCTYPE/');
        $app->run();
        $this->assertEquals(26, $this->var);
    }
}
