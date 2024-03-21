<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Exception;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Lister;
use Atk4\Ui\View;

class ListerTest extends TestCase
{
    use CreateAppTrait;

    /**
     * @doesNotPerformAssertions
     */
    public function testListerRender(): void
    {
        $view = new View();
        $view->setApp($this->createApp());
        $view->invokeInit();
        $lister = Lister::addTo($view, ['defaultTemplate' => 'lister.html']);
        $lister->setSource(['foo', 'bar']);
    }

    /**
     * Or clone lister's template from parent.
     */
    public function testListerRender2(): void
    {
        $view = new View(['template' => new HtmlTemplate('hello{list}, world{/list}')]);
        $view->setApp($this->createApp());
        $view->invokeInit();
        $lister = Lister::addTo($view, [], ['list']);
        $lister->setSource(['foo', 'bar']);
        self::assertSame('hello, world, world', $view->render());
    }

    public function testAddAfterRender(): void
    {
        $view = new View();
        $view->setApp($this->createApp());
        $view->invokeInit();

        $this->expectException(Exception::class);
        Lister::addTo($view);
    }
}
