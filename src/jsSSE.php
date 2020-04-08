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

        if ($_GET['__atk_sse'] ?? null) {
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
            $this->sendEvent('js', $this->app->encodeJson(['success' => $success, 'message' => 'Success', 'atkjs' => $ajaxec]), 'jsAction');
        } else {
            // ignore event
        }
    }

    public function terminate($ajaxec, $msg = null, $success = true)
    {
        if ($this->browserSupport) {
            if ($ajaxec) {
                $this->sendEvent(
                    'js',
                    $this->app->encodeJson(['success' => $success, 'message' => 'Success', 'atkjs' => $ajaxec]),
                    'jsAction'
                );
            }

            // no further output please
            $this->app->terminate();
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
    public function flush(): void
    {
        flush();
    }

    /**
     * Send Data.
     */
    private function output(string $content): void
    {
        if ($this->echoFunction) {
            call_user_func($this->echoFunction, $content);
            return;
        }

        // output headers and content
        $app = $this->app;
        \Closure::bind(static function () use ($app, $content): void {
            $app->outputResponse($content, []);
        }, null, App::class)();
    }

    /**
     * Send a SSE data block.
     */
    public function sendBlock(string $id, string $data, string $name = null): void
    {
        $this->output('id: ' . $id . "\n");
        if (strlen($name) > 0) {
            $this->output('event: ' . $name . "\n");
        }
        $this->output($this->wrapData($data) . "\n");
        $this->flush();
    }

    /**
     * Create SSE data string.
     */
    private function wrapData(string $string): string
    {
        return implode('', array_map(function ($v) {
            return 'data: ' . $v . "\n";
        }, preg_split('~\r?\n|\r~', $string)));
    }

    protected function initSse()
    {
        @set_time_limit(0); // disable time limit

        $this->app->setResponseHeader('content-type', 'text/event-stream');

        // disable caching
        $this->app->setResponseHeader('cache-control', 'no-store');
        // for nginx, see http://nginx.org/en/docs/http/ngx_http_proxy_module.html#proxy_buffers
        $this->app->setResponseHeader('x-accel-buffering', 'no');

        // disable compression
        @ini_set('zlib.output_compression', 0);
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }

        // prevent buffering
        if (ob_get_level()) {
            ob_end_flush();
        }
    }
}
