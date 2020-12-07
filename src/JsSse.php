<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\HookTrait;

/**
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 */
class JsSse extends JsCallback
{
    use HookTrait;

    /** @const string Executed when user aborted, or disconnect browser, when using this SSE. */
    public const HOOK_ABORTED = self::class . '@connection_aborted';

    /** @var bool Allows us to fall-back to standard functionality of JsCallback if browser does not support SSE. */
    public $browserSupport = false;

    /** @var bool Show Loader when doing sse. */
    public $showLoader = false;

    /** @var bool add window.beforeunload listener for closing js EventSource. Off by default. */
    public $closeBeforeUnload = false;

    /** @var bool Keep execution alive or not if connection is close by user. False mean that execution will stop on user aborted. */
    public $keepAlive = false;

    /** @var \Closure custom function for outputting (instead of echo) */
    public $echoFunction;

    protected function init(): void
    {
        parent::init();

        if ($_GET['__atk_sse'] ?? null) {
            $this->browserSupport = true;
            $this->initSse();
        }
    }

    public function jsRender(): string
    {
        $this->getApp(); // assert has App

        $options = ['uri' => $this->getJsUrl()];
        if ($this->showLoader) {
            $options['showLoader'] = $this->showLoader;
        }
        if ($this->closeBeforeUnload) {
            $options['closeBeforeUnload'] = $this->closeBeforeUnload;
        }

        return (new Jquery())->atkServerEvent($options)->jsRender();
    }

    /**
     * Sending an sse action.
     */
    public function send($action, $success = true)
    {
        if ($this->browserSupport) {
            $ajaxec = $this->getAjaxec($action);
            $this->sendEvent('js', $this->getApp()->encodeJson(['success' => $success, 'message' => 'Success', 'atkjs' => $ajaxec]), 'atk_sse_action');
        }
    }

    public function terminateAjax($ajaxec, $msg = null, $success = true)
    {
        if ($this->browserSupport) {
            if ($ajaxec) {
                $this->sendEvent(
                    'js',
                    $this->getApp()->encodeJson(['success' => $success, 'message' => 'Success', 'atkjs' => $ajaxec]),
                    'atk_sse_action'
                );
            }

            // no further output please
            $this->getApp()->terminate();
        }

        $this->getApp()->terminateJson(['success' => $success, 'message' => 'Success', 'atkjs' => $ajaxec]);
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
            ($this->echoFunction)($content);

            return;
        }

        // output headers and content
        $app = $this->getApp();
        \Closure::bind(static function () use ($app, $content): void {
            $app->outputResponse($content, []);
        }, null, $app)();
    }

    /**
     * Send a SSE data block.
     */
    public function sendBlock(string $id, string $data, string $name = null): void
    {
        if (connection_aborted()) {
            $this->hook(self::HOOK_ABORTED);

            // stop execution when aborted if not keepAlive.
            if (!$this->keepAlive) {
                $this->getApp()->callExit();
            }
        }

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

    /**
     * Initialise this sse.
     * It will ignore user abort by default.
     */
    protected function initSse()
    {
        @set_time_limit(0); // disable time limit
        ignore_user_abort(true);

        $this->getApp()->setResponseHeader('content-type', 'text/event-stream');

        // disable buffering for nginx, see http://nginx.org/en/docs/http/ngx_http_proxy_module.html#proxy_buffers
        $this->getApp()->setResponseHeader('x-accel-buffering', 'no');

        // disable compression
        @ini_set('zlib.output_compression', '0');
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }

        // prevent buffering
        if (ob_get_level()) {
            ob_end_flush();
        }
    }
}
