<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Ui\AbstractView;
use Atk4\Ui\App;
use Atk4\Ui\Callback;
use Atk4\Ui\Layout;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;

trait CreateAppTrait
{
    /**
     * @param array<0|string, mixed> $seed
     */
    protected function createApp(array $seed = []): App
    {
        $appClass = $seed[0] ?? App::class;
        unset($seed[0]);

        if (!isset($seed['request'])) {
            $seed['request'] = (new Psr17Factory())->createServerRequest('GET', '/');
        }

        $app = new $appClass(array_merge([
            'catchExceptions' => false,
            'alwaysRun' => false,
        ], $seed));
        $app->initLayout([Layout::class]);

        return $app;
    }

    protected function triggerCallback(ServerRequestInterface $request, Callback $cb, string $triggerValue = '1'): ServerRequestInterface
    {
        return $request->withQueryParams(array_merge(
            $request->getQueryParams(),
            [
                Callback::URL_QUERY_TRIGGER_PREFIX . $cb->getUrlTrigger() => $triggerValue,
                Callback::URL_QUERY_TARGET => $cb->getUrlTrigger(),
            ]
        ));
    }

    /**
     * @template T of App
     *
     * @param \Closure(ServerRequestInterface): T $createAppFx
     * @param \Closure(T): ServerRequestInterface $simulateRequestFx
     *
     * @return T
     */
    protected function simulateAppCallback(\Closure $createAppFx, \Closure $simulateRequestFx): App
    {
        $requestBase = (new Psr17Factory())->createServerRequest('GET', '/');
        $appBase = $createAppFx($requestBase);
        $request = $simulateRequestFx($appBase);

        $app = $createAppFx($request);

        return $app;
    }

    /**
     * @template T of AbstractView
     *
     * @param \Closure(ServerRequestInterface): T $createViewFx
     * @param \Closure(T): ServerRequestInterface $simulateRequestFx
     *
     * @return T
     */
    protected function simulateViewCallback(\Closure $createViewFx, \Closure $simulateRequestFx): AbstractView
    {
        $view = null;
        $this->simulateAppCallback(static function (ServerRequestInterface $request) use ($createViewFx, &$view) {
            $view = $createViewFx($request);

            return $view->getApp();
        }, static function () use ($simulateRequestFx, &$view) {
            return $simulateRequestFx($view);
        });

        return $view;
    }
}
