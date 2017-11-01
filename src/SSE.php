<?php

namespace atk4\ui;

use atk4\core\AppScopeTrait;
use atk4\core\DIContainerTrait;
use atk4\core\TrackableTrait;
use atk4\core\InitializerTrait;

/**
 * Class SSE
 *
 * @package atk4\ui
 */

class SSE extends View
{

    /*use TrackableTrait;
    use AppScopeTrait;
    use DIContainerTrait;
    use InitializerTrait;*/

    public $view;
    public $events;
    public $ui = '';
    public $cb;

    public $defaults =[
        'sleep_time'            => 0.5,                 // seconds to sleep after the data has been sent
        'exec_limit'            => 600,                 // the time limit of the script in seconds
        'client_reconnect'      => 1,                   // the time client to reconnect after connection has lost in seconds
        'allow_cors'            => false,               // Allow Cross-Origin Access?
        'keep_alive_time'       => 300,                 // The interval of sending a signal to keep the connection alive
        'is_reconnect'          => false,               // A read-only flag indicates whether the user reconnects
        'use_chunked_encoding'  => false,               // Allow chunked encoding
    ];

    public $start;
//    public function __construct($defaults = [])
//    {
//        //$this->setDefaults($defaults);
//        $t= 't';
//        /*$this->view = $view;
//        $this->view->js(true)->atkServerEvent([
//            'uri' => './sse.php'
//        ]);*/
//    }

    public function init()
    {
        parent::init();
        $this->cb = $this->_add('CallbackLater');

        $this->cb->set(function () {
            if ($this->cb->triggered) {
                $this->sendSse();
            }
        });

        $chain = new jsChain();

        $this->js(true, $chain->atkServerEvent(
            ['uri' => $this->cb->getUrl()]
        ));

        /*$this->_initialized = true;
        $this->view = $this->owner;
        $this->cb = $this->view->add('Callback');
        $this->view->js(true)->atkServerEvent([
            'uri' => './sse.php'
        ]);*/

    }

//    public function set($fx = [], $args = null)
//    {
//        if (!is_callable($fx)) {
//            throw new Exception('Error: Need to pass a callable function to Loader::set()');
//        }
//
//        $this->cb->set(function () use ($fx) {
//            call_user_func($fx, $this->view);
//            $this->sendSse();
//            //$this->app->terminate($this->renderJSON());
//        });
//
//        return $this;
//    }

    public function sendSse()
    {
//        header('Content-Type: text/event-stream');
//        header('Cache-Control: no-cache');

        $this->initSse();
        $this->setStart(time());
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Cache-Control: private');
        //header('Content-Encoding: none;');
        header('Pragma: no-cache');

//        $time = date('r');
//        echo "data: The server time is: {$time}\n\n";

        echo "retry: 3000\n";
        $v = new View(['ui'=>'segement']);
        $t = $v->renderJSON();
        $this->sendBlock('1000', $t, null);
        $this->flush();
//        $this->initSse();
//        $this->setStart(time());
//        header('Content-Type: text/event-stream');
//        header('Cache-Control: no-cache');
//        header('Cache-Control: private');
//        header('Content-Encoding: none;');
//        header('Pragma: no-cache');
//
//        echo "retry: 1000\n";
//
//        if ($this->isTick()) {
//            // No updates needed, send a comment to keep the connection alive.
//            // From https://developer.mozilla.org/en-US/docs/Server-sent_events/Using_server-sent_events
//            echo ': ' . sha1(mt_rand()) . "\n\n";
//        }
//        $time = time();
//        echo "data: The server time is: {$time}\n\n";
//        flush();
         exit;
//
//        // put into sleep when in loop.
//        // $this->sleep();

    }


    /**
     * Send Data in buffer to client
     */
    public function flush()
    {
        @ob_flush();
        @flush();
    }

    /**
     * Send Data
     *
     * @param string $content
     */
    private function send($content)
    {
        print($content);
    }
    /**
     * Send a SSE data block
     *
     * @param mixed $id Event ID
     * @param string $data Event Data
     * @param string $name Event Name
     */
    public function sendBlock($id, $data, $name = null)
    {
        $this->send("id: {$id}\n");
        if (strlen($name) && $name !== null) {
            $this->send("event: {$name}\n");
        }
        $this->send($this->wrapData($data) . "\n\n");
    }
    /**
     * Create SSE data string
     *
     * @param string $string data to be processed
     * @return string
     */
    private function wrapData($string)
    {
        return 'data:' . str_replace("\n","\ndata: ", $string);
    }

    /**
     * Sleep the process
     */
    public function sleep()
    {
        usleep($this->defaults[sleep_time] * 1000000);
    }

    public function setStart($time)
    {
        $this->start = $time;
    }

    /**
     * Get time start
     * @return int
     */
    public function getUptime()
    {
        return time() - $this->start;
    }

    /**
     * Get the number tick
     * @return bool
     */
    public function isTick()
    {
        return $this->getUptime() % $this->defaults[keep_alive_time] === 0;
    }

    public function addEvent(View $view, $callable)
    {

    }

    /**
     * SSE is not rendered normally. It's invisible.
     * It will only expose a Callback url to activate sse event.
     */
    public function getHTML()
    {
    }

    public function start()
    {
        $start =  new jsChain();
        return $start->atkServerEvent([
            'uri' => './sse.php'
        ]);
    }

    protected function initSse()
    {
        @set_time_limit(0); // Disable time limit
        // Prevent buffering
        if(function_exists('apache_setenv')){
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        while (ob_get_level() != 0) {
            ob_end_flush();
        }
        ob_implicit_flush(1);
    }
}