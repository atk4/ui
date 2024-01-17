<?php

declare(strict_types=1);

namespace Atk4\Ui\Repro;

class Repro
{
    public function repro(): void
    {
        if (\PHP_INT_SIZE === 4) {
            self::foo();
        }

        self::foo();
    }

    public static function foo(): void
    {
    }
}
