<?php

declare(strict_types=1);

namespace Atk4\Ui\Exception;

if (\PHP_SAPI === 'cli') {
    /**
     * @internal
     */
    final class LateOutputCliOnlyError extends \Error
    {
    }
}
