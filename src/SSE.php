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
     * @param callable $callback
     * @param array $args
     *
     * @throws Exception
     *
     * @return mixed|null
     */
    public function set($callback, $args = [])
    {
        if (!$this->app) {
            throw new Exception(['Call-back must be part of a RenderTree']);
        }

        $this->view = $this->owner;

        if ($this->triggered()) {
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
        return $_GET[$this->name] ?? false;
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
     * This method is for consistency. You should call SSE through JavaScript.
     */
    public function getURL($mode = 'sse')
    {
        throw new Exception('SSE should only be loaded through JavaScript, not directly');
    }

    public function sendSse()
    {
        $this->initSse();
        $this->setStart(time());

        $this->send("retry: ".$this->clientReconnect * 1000 ."\n");

        $this->sendBlock('1000', $this->view->renderJSON(), null);
    }

    /**
     * Send Data in buffer to client.
     */
    public function flush()
    {
        // do not flush if is in testing
        if (defined('UNIT_TESTING')) {
            return;
        }

        if (ob_get_level() > 0) {
            ob_end_flush();
        }
    }

    /**
     * Send Data.
     *
     * @param string $content
     */
    private function send($content)
    {
        if (!headers_sent()) {
            $this->sendHeaders();
        }

        echo $content;

        $this->flush();
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
        $sleepTime = $this->defaults["sleep_time"] ?? $this->sleepTime;

        usleep($sleepTime * 1000000);
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
        $keepAliveTime = $this->defaults['keep_alive_time'] ?? $this->keepAliveTime;
        return $this->getUptime() % $keepAliveTime === 0;
    }

    protected function initSse()
    {
        @set_time_limit(0); // Disable time limit

        // Prevent buffering
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }

        if (ob_get_level()) {
            ob_end_clean();
        }
    }

    protected function sendHeaders()
    {
        @ini_set('zlib.output_compression', 0);

        header('Content-Type: text/event-stream');

        header('Cache-Control: no-cache');
        header('Cache-Control: private');

        //header('Content-Encoding: none;');

        header('Pragma: no-cache');

        // nginx @http://nginx.org/en/docs/http/ngx_http_proxy_module.html#proxy_buffers
        header('X-Accel-Buffering: no');
    }
}
