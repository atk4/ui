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
    /**
     * @doesNotPerformAssertions
     */
    public function testListerRender(): void
    {
        $v = new View();
        $v->invokeInit();
        $l = Lister::addTo($v, ['defaultTemplate' => 'lister.html']);
        $l->setSource(['foo', 'bar']);
    }

    /**
     * Or clone lister's template from parent.
     */
    public function testListerRender2(): void
    {
        $v = new View(['template' => new HtmlTemplate('hello{list}, world{/list}')]);
        $v->invokeInit();
        $l = Lister::addTo($v, [], ['list']);
        $l->setSource(['foo', 'bar']);
        static::assertSame('hello, world, world', $v->render());
    }

    public function testAddAfterRender(): void
    {
        $v = new View();
        $v->invokeInit();

        $this->expectException(Exception::class);
        Lister::addTo($v);
    }
}
