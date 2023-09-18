<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Ui\App;
use Atk4\Ui\Callback;
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

        $this->setGlobalsFromRequest($seed['request']);

        return new $appClass(array_merge([
            'catchExceptions' => false,
            'alwaysRun' => false,
        ], $seed));
    }

    /**
     * TODO remove in https://github.com/atk4/ui/pull/2101.
     */
    protected function setGlobalsFromRequest(ServerRequestInterface $request): void
    {
        $_GET = $request->getQueryParams();
        $_POST = $request->getParsedBody() ?? [];
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
}
