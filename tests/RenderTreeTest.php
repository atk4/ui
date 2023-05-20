<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\View;

/**
 * Multiple tests to ensure that adding views through various patterns initializes them
 * nicely still.
 */
class RenderTreeTest extends TestCase
{
    use CreateAppTrait;

    public function testBasic(): void
    {
        $view = new View();
        $view->setApp($this->createApp());
        $view->render();

        $view->getApp();
        self::assertNotNull($view->template);
    }

    public function testBasicNested(): void
    {
        $view = new View();
        $view2 = View::addTo($view);
        $view->setApp($this->createApp());
        $view->render();

        $view2->getApp();
        self::assertNotNull($view2->template);

        self::assertSame($view2->getApp(), $view->getApp());
    }
}
