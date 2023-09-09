<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Ui\App;

trait CreateAppTrait
{
    /**
     * @param array<string, mixed> $defaults
     */
    protected function createApp(array $defaults = []): App
    {
        return new App(array_merge([
            'catchExceptions' => false,
            'alwaysRun' => false,
        ], $defaults));
    }
}
