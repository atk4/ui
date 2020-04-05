<?php

namespace atk4\ui;

/*
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 */

class jsSSE extends jsCallback
{
    // Allows us to fall-back to standard functionality of jsCallback if browser does not support SSE
    public $browserSupport = false;
    public $showLoader = false;

    /**
     * @var callable - custom function for outputting (instead of echo)
     */
    public $echoFunction = null;

    public function init()
    {
        parent::init();

        $event = $_GET['event'] ?? null;

        if ($event === 'sse') {
            $this->browserSupport = true;
            $this->initSse();
        }
    }

    public function jsRender()
    {
        if (!$this->app) {
            throw new Exception(['Call-back must be part of a RenderTree']);
        }

        $options = ['uri' => $this->getJSURL()];
        if ($this->showLoader) {
            $options['showLoader'] = $this->showLoader;
        }

        return (new jQuery())->atkServerEvent($options)->jsRender();
    }

    public function send($action, $success = true)
    {
        if ($this->browserSupport) {
            $ajaxec = $this->getAjaxec($action);
            $this->sendEvent('js', json_encode(['success' => $success, 'message' => 'Success', 'atkjs' => $ajaxec]), 'jsAction');
        } // else ignore event
    }

    public function terminateJSON($ajaxec, $msg = null, $success = true)
    {
        if ($this->browserSupport) {
            if ($ajaxec) {
                $this->sendEvent(
                    'js',
                    json_encode(['success' => $success, 'message' => 'Success', 'atkjs' => $ajaxec]),
                    'jsAction'
                );
            }

            // no further output please
            $this->app->terminateJSON('');
        }

        $this->app->terminateJSON(['success' => $success, 'message' => 'Success', 'atkjs' => $ajaxec]);
    }

    /**
     * Output a SSE Event.
     */
    public function sendEvent($id, $data, $eventName)
    {
        $this->sendBlock($id, $data, $eventName);
    }

    /**
     * Send Data in buffer to client.
     */
    public function flush()
    {
        @flush();
    }

    /**
     * Send Data.
     *
     * @param string $content
     */
    private function output($content)
    {
        if (!headers_sent()) {
            $this->sendHeaders();
        }

        if ($this->echoFunction) {
            call_user_func($this->echoFunction, $content);
        } else {
            echo $content;
        }
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
        $this->output("id: {$id}\n");
        if (strlen($name) && $name !== null) {
            $this->output("event: {$name}\n");
        }
        $this->output($this->wrapData($data) . "\n\n");
        $this->flush();
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
        return 'data:' . str_replace("\n", "\ndata: ", $string);
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
        @ini_set('output_buffering', false);
        @ini_set('implicit_flush', 1);

        header('Content-Type: text/event-stream');

        header('Cache-Control: no-cache');
        header('Cache-Control: private');
        //header('Connection: keep-alive');
        //header('Content-Encoding: none;');

        header('Pragma: no-cache');

        // nginx @http://nginx.org/en/docs/http/ngx_http_proxy_module.html#proxy_buffers
        header('X-Accel-Buffering: no');
    }
}
