<?php

namespace atk4\ui\tests;

class CallbackTest extends \atk4\core\PHPUnit_AgileTestCase
{
    /**
     * Test constructor.
     */
    private $regex = '/^..DOCTYPE/';

    public $app;

    public function setUp()
    {
        $this->app = new \atk4\ui\App(['always_run'=>false]);
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

        // simulate triggering
        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertEquals(null, $var);
    }

    public function testCallbackPOST()
    {
        $var = null;

        $app = $this->app;

        $cb = $app->add(['Callback', 'POST_trigger'=>true]);

        // simulate triggering
        $_POST[$cb->name] = true;

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

        // simulate triggering
        $cb->set(function ($x) use (&$var) {
            $var = $x;
        }, [34]);

        $this->assertEquals(null, $var);

        $this->expectOutputRegex($this->regex);
        $app->run();

        $this->assertEquals(null, $var);
    }
}
