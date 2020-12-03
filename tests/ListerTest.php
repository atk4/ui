<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;
use Atk4\Ui\Exception;
use Atk4\Ui\HtmlTemplate;

class ListerTest extends AtkPhpunit\TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testListerRender()
    {
        $v = new \Atk4\Ui\View();
        $v->invokeInit();
        $l = \Atk4\Ui\Lister::addTo($v, ['defaultTemplate' => 'lister.html']);
        $l->setSource(['foo', 'bar']);
    }

    /**
     * Or clone lister's template from parent.
     */
    public function testListerRender2()
    {
        $v = new \Atk4\Ui\View(['template' => new HtmlTemplate('hello{list}, world{/list}')]);
        $v->invokeInit();
        $l = \Atk4\Ui\Lister::addTo($v, [], ['list']);
        $l->setSource(['foo', 'bar']);
        $this->assertSame('hello, world, world', $v->render());
    }

    public function testAddAfterRender()
    {
        $this->expectException(Exception::class);
        $v = new \Atk4\Ui\View();
        $v->invokeInit();
        $l = \Atk4\Ui\Lister::addTo($v);
        $l->setSource(['foo', 'bar']);
    }
}
