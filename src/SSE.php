<?php

namespace atk4\ui;

use atk4\core\AppScopeTrait;
use atk4\core\DIContainerTrait;
use atk4\core\InitializerTrait;
use atk4\core\TrackableTrait;

/**
 * Class SSE.
 */
class SSE
{
    use TrackableTrait;
    use AppScopeTrait;
    use DIContainerTrait;
    use InitializerTrait;

    public $view = null;
    public $start;
    public $runEvent = false;
    public $isRunning = false;

    //Connection defaults.
    public $sleepTime = 0.5;            //seconds to sleep after the data has been sent.
    public $execLimit = 600;            //the time limit of the script in seconds.
    public $clientReconnect = 2;        //the time client to reconnect after connection has lost in seconds.
    public $allowCors = false;          //Allow Cross-Origin Access.
    public $keepAliveTime = 300;        //The interval of sending a signal to keep the connection alive.
    public $isReconnect = false;        //A read-only flag indicates whether the user reconnects.
    public $useChunkEncoding = false;   //Allow chunked encoding

    public function init()
    {
        if ($this->runEvent) {
            $chain = new jsChain();
            $this->app->html->js(true, $chain->atkServerEvent(
                ['uri' => $this->getUrl()]
            ));
            $this->isRunning = true;
        }

        $this->_initialized = true;
    }

    /**
     * Executes user-specified action when call-back is triggered.
     *
     * @param callback $callback
     * @param array    $args
     *
     * @return mixed|null
     */
    public function set($callback, $args = [])
    {
        if (!$this->app) {
            throw new Exception(['Call-back must be part of a RenderTree']);
        }

        $this->view = $this->owner;

        if (isset($_GET[$this->name])) {
            $this->triggered = $_GET[$this->name];

            $this->app->run_called = true;
            $this->app->stickyGet($this->name);
            $ret = call_user_func_array($callback, $args);
            $this->sendSse();

            return $ret;
        }

        return $this;
    }

    public function run()
    {
        $chain = new jsChain();

        return $chain->atkServerEvent(['uri' => $this->getUrl()]);
    }

    /**
     * Is callback triggered?
     *
     * @return bool
     */
    public function triggered()
    {
        return isset($_GET[$this->name]) ? $_GET[$this->name] : false;
    }

    /**
     * Return URL that will trigger action on this call-back.
     *
     * @param string $mode
     *
     * @return string
     */
    public function getJSURL($mode = 'sse')
    {
        return $this->app->jsURL([$this->name => $mode]);
    }

    /**
     * This method is for consistency. You should call SSE through JavaScript
     */
    public function getURL($mode = 'sse')
    {
        return $this->getJSURL($mode);
    }

    public function sendSse()
    {
        $this->initSse();
        $this->setStart(time());
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Cache-Control: private');
        //header('Content-Encoding: none;');
        header('Pragma: no-cache');

        echo 'retry: '.$this->clientReconnect * 1000 ."\n";

        $this->sendBlock('1000', $this->view->renderJSON(), null);
        $this->flush();
    }

    /**
     * Send Data in buffer to client.
     */
    public function flush()
    {
        @ob_flush();
        @flush();
    }

    /**
     * Send Data.
     *
     * @param string $content
     */
    private function send($content)
    {
        echo $content;
    }

    /**
     * Send a SSE data block.
     *
     * @param mixed  $id   Event ID
     * @param string $data Event Data
     * @param string $name Event Name
     */
    public function sendBlock($id, $data, $name = null)
    {
        $this->send("id: {$id}\n");
        if (strlen($name) && $name !== null) {
            $this->send("event: {$name}\n");
        }
        $this->send($this->wrapData($data)."\n\n");
    }

    /**
     * Create SSE data string.
     *
     * @param string $string data to be processed
     *
     * @return string
     */
    private function wrapData($string)
    {
        return 'data:'.str_replace("\n", "\ndata: ", $string);
    }

    /**
     * Sleep the process.
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
     * Get time start.
     *
     * @return int
     */
    public function getUptime()
    {
        return time() - $this->start;
    }

    /**
     * Get the number tick.
     *
     * @return bool
     */
    public function isTick()
    {
        return $this->getUptime() % $this->defaults[keep_alive_time] === 0;
    }

    protected function initSse()
    {
        @set_time_limit(0); // Disable time limit
        // Prevent buffering
        if (function_exists('apache_setenv')) {
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
