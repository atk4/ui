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

    /** @var bool Allows us to fall-back to standard functionality of JsCallback if browser does not support SSE. */
    public $browserSupport = false;

    /** @var bool Show Loader when doing SSE. */
    public $showLoader = false;

    /** @var bool add window.beforeunload listener for closing js EventSource. Off by default. */
    public $closeBeforeUnload = false;

    /** @var \Closure|null custom function for outputting (instead of echo) */
    public $echoFunction;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        if ($this->getApp()->tryGetRequestQueryParam('__atk_sse')) {
            $this->browserSupport = true;
            $this->initSse();
        }
    }

    #[\Override]
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

    #[\Override]
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

    protected function initSse(): void
    {
        $this->getApp()->setResponseHeader('content-type', 'text/event-stream');

        // disable buffering for nginx, see https://nginx.org/en/docs/http/ngx_http_proxy_module.html#proxy_buffers
        $this->getApp()->setResponseHeader('x-accel-buffering', 'no');

        // prevent buffering
        while (ob_get_level() > 0) {
            // workaround flush() called by ob_end_flush() when zlib.output_compression is enabled
            // https://github.com/php/php-src/issues/13798
            if (ob_get_length() === 0) {
                ob_end_clean();
            } else {
                ob_end_flush();
            }
        }
    }

    /**
     * Sending an SSE action.
     */
    public function send(JsExpressionable $action, bool $success = true): void
    {
        if ($this->browserSupport) {
            $ajaxec = $this->getAjaxec($action);
            $this->sendEvent('js', $this->getApp()->encodeJson(['success' => $success, 'atkjs' => $ajaxec->jsRender()]), 'atkSseAction');
        }
    }

    /**
     * @return never
     */
    #[\Override]
    public function terminateAjax(JsBlock $ajaxec, $msg = null, bool $success = true): void
    {
        $ajaxecStr = $ajaxec->jsRender();

        if ($this->browserSupport) {
            if ($ajaxecStr !== '') {
                $this->sendEvent(
                    'js',
                    $this->getApp()->encodeJson(['success' => $success, 'atkjs' => $ajaxecStr]),
                    'atkSseAction'
                );
            }

            // no further output please
            $this->getApp()->terminate();
        }

        $this->getApp()->terminateJson(['success' => $success, 'atkjs' => $ajaxecStr]);
    }

    /**
     * Output a SSE Event.
     */
    public function sendEvent(string $id, string $data, ?string $eventName = null): void
    {
        $this->sendBlock($id, $data, $eventName);
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

    /**
     * Send Data in buffer to client.
     */
    public function flush(): void
    {
        flush();
    }

    public function sendBlock(string $id, string $data, ?string $eventName = null): void
    {
        $this->output('id: ' . $id . "\n");
        if ($eventName !== null) {
            $this->output('event: ' . $eventName . "\n");
        }
        $this->output($this->wrapData($data) . "\n");
        $this->flush();
    }
}
