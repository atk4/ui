<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Ui\App;

trait CreateAppTrait
{
    use ReplaceAppRequestTrait;

    protected function createApp(): App
    {
        return new App([
            'catchExceptions' => false,
            'alwaysRun' => false,
        ]);
    }
}
