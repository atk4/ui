<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Table\Column;

class TableColumnTest extends TestCase
{
    public function testTooManyArgumentsConstructorError(): void
    {
        $this->expectException(\Error::class);
        new Column([], []);
    }
}
