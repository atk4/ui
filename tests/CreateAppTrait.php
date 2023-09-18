<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Ui\App;
use Nyholm\Psr7\Factory\Psr17Factory;

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

        return new $appClass(array_merge([
            'catchExceptions' => false,
            'alwaysRun' => false,
        ], $seed));
    }
}
