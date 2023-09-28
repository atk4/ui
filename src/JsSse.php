<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\HookTrait;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpressionable;

class JsSse extends JsCallback
{
    use HookTrait;

    /** Executed when user aborted, or disconnect browser, when using this SSE. */
    public const HOOK_ABORTED = self::class . '@connectionAborted';

    /** @var bool Allows us to fall-back to standard functionality of JsCallback if browser does not support SSE. */
    public $browserSupport = false;

    /** @var bool Show Loader when doing sse. */
    public $showLoader = false;

    /** @var bool add window.beforeunload listener for closing js EventSource. Off by default. */
    public $closeBeforeUnload = false;

    /** @var bool Keep execution alive or not if connection is close by user. False mean that execution will stop on user aborted. */
    public $keepAlive = false;

    /** @var \Closure|null custom function for outputting (instead of echo) */
    public $echoFunction;

    protected function init(): void
    {
        parent::init();

        if ($this->getApp()->tryGetRequestQueryParam('__atk_sse')) {
            $this->browserSupport = true;
            $this->initSse();
        }
    }

    public function jsExecute(): JsBlock
    {
        $this->assertIsInitialized();

        $options = ['url' => $this->getJsUrl()];
        if ($this->showLoader) {
            $options['showLoader'] = $this->showLoader;
        }
        if ($this->closeBeforeUnload) {
            $options['closeBeforeUnload'] = $this->closeBeforeUnload;
        }

        return new JsBlock([(new Jquery($this->getOwner() /* TODO element and loader element should be passed explicitly */))->atkServerEvent($options)]);
    }

    public function set($fx = null, $args = null)
    {
        if (!$fx instanceof \Closure) {
            throw new \TypeError('$fx must be of type Closure');
        }

        return parent::set(static function (Jquery $chain) use ($fx, $args) {
            // TODO replace EventSource to support POST
            // https://github.com/Yaffle/EventSource
            // https://github.com/mpetazzoni/sse.js
            // https://github.com/EventSource/eventsource
            // https://github.com/byjg/jquery-sse
            return $fx($chain, ...array_values($args ?? []));
        });
    }

    /**
     * Sending an SSE action.
     */
    public function send(JsExpressionable $action, bool $success = true): void
    {
        if ($this->browserSupport) {
            $ajaxec = $this->getAjaxec($action);
            $this->sendEvent('js', $this->getApp()->encodeJson(['success' => $success, 'atkjs' => $ajaxec]), 'atkSseAction');
        }
    }

    /**
     * @return never
     */
    public function terminateAjax($ajaxec, $msg = null, bool $success = true): void
    {
        if ($this->browserSupport) {
            if ($ajaxec) {
                $this->sendEvent(
                    'js',
                    $this->getApp()->encodeJson(['success' => $success, 'atkjs' => $ajaxec]),
                    'atkSseAction'
                );
            }

            // no further output please
            $this->getApp()->terminate();
        }

        $this->getApp()->terminateJson(['success' => $success, 'atkjs' => $ajaxec]);
    }

    /**
     * Output a SSE Event.
     */
    public function sendEvent(string $id, string $data, string $eventName = null): void
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

    private function output(string $content): void
    {
        if ($this->echoFunction) {
            ($this->echoFunction)($content);

            return;
        }

        // output headers and content
        $app = $this->getApp();
        \Closure::bind(static function () use ($app, $content): void {
            $app->outputResponse($content);
        }, null, $app)();
    }

    public function sendBlock(string $id, string $data, string $eventName = null): void
    {
        if (connection_aborted()) {
            $this->hook(self::HOOK_ABORTED);

            // stop execution when aborted if not keepAlive
            if (!$this->keepAlive) {
                $this->getApp()->callExit();
            }
        }

        $this->output('id: ' . $id . "\n");
        if ($eventName !== null) {
            $this->output('event: ' . $eventName . "\n");
        }
        $this->output($this->wrapData($data) . "\n");
        $this->flush();
    }

    /**
     * Create SSE data string.
     */
    private function wrapData(string $string): string
    {
        return implode('', array_map(static function (string $v): string {
            return 'data: ' . $v . "\n";
        }, preg_split('~\r?\n|\r~', $string)));
    }

    /**
     * It will ignore user abort by default.
     */
    protected function initSse(): void
    {
        @set_time_limit(0); // disable time limit
        ignore_user_abort(true);

        $this->getApp()->setResponseHeader('content-type', 'text/event-stream');

        // disable buffering for nginx, see https://nginx.org/en/docs/http/ngx_http_proxy_module.html#proxy_buffers
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
