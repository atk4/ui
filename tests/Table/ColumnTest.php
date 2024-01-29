<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests\Table;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;
use Atk4\Ui\Tests\CreateAppTrait;
use Atk4\Ui\View;

class ColumnTest extends TestCase
{
    use CreateAppTrait;

    public function testAssertColumnViewNotInitializedException(): void
    {
        $column = new Table\Column\ActionButtons();
        $column->name = 'foo';

        $view = new View();
        $view->setApp($this->createApp());
        $view->invokeInit();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unexpected initialized View instance');
        $column->addButton($view);
    }
}
