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

    private string $lastSentId = '';

    /** @var bool Show Loader when doing SSE. */
    public $showLoader = false;

    /** @var bool Add window.beforeunload listener for closing js EventSource. Off by default. */
    public $closeBeforeUnload = false;

    /** @var \Closure(string): void|null Custom function for outputting (instead of echo). */
    public $echoFunction;

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

        return parent::set(function (Jquery $chain) use ($fx, $args) {
            $this->initSse();

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

        // disable buffering for nginx
        // https://nginx.org/en/docs/http/ngx_http_proxy_module.html#proxy_buffers
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
    public function send(JsExpressionable $action): void
    {
        $ajaxec = $this->getAjaxec($action);
        $this->sendEvent(
            '',
            $this->getApp()->encodeJson([
                'success' => true,
                'atkjs' => $ajaxec->jsRender(),
            ]),
            'atkSseAction'
        );
    }

    /**
     * @return never
     */
    #[\Override]
    protected function terminateAjaxIfCanTerminate(JsBlock $ajaxec): void
    {
        $ajaxecStr = $ajaxec->jsRender();
        if ($ajaxecStr !== '') {
            $this->sendEvent(
                '',
                $this->getApp()->encodeJson([
                    'success' => true,
                    'atkjs' => $ajaxecStr,
                ]),
                'atkSseAction'
            );
        }

        $this->getApp()->terminate();
    }

    protected function flush(): void
    {
        flush();
    }

    private function outputEventResponse(string $content): void
    {
        if ($this->echoFunction) {
            ($this->echoFunction)($content);

            return;
        }

        // workaround flush() ignored by Apache mod_proxy_fcgi
        // https://stackoverflow.com/questions/30707792/how-to-disable-buffering-with-apache2-and-mod-proxy-fcgi#36298336
        // https://bz.apache.org/bugzilla/show_bug.cgi?id=68827
        $content .= ': ' . str_repeat('x', 4_096) . "\n\n";

        $app = $this->getApp();
        \Closure::bind(static function () use ($app, $content): void {
            $app->outputResponse($content);
        }, null, $app)();

        $this->flush();
    }

    protected function sendEvent(string $id, string $data, ?string $name = null): void
    {
        $content = '';
        if ($id !== '' || $this->lastSentId !== '') {
            $content = 'id: ' . $id . "\n";
        }
        if ($name !== null) {
            $content .= 'event: ' . $name . "\n";
        }
        $content .= implode('', array_map(static function (string $v): string {
            return 'data: ' . $v . "\n";
        }, preg_split('~\r?\n|\r~', $data)));
        $content .= "\n";

        $this->outputEventResponse($content);

        $this->lastSentId = $id;
    }
}
