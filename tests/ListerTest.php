<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\ui\Exception;

class ListerTest extends AtkPhpunit\TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testListerRender()
    {
        $v = new \atk4\ui\View();
        $v->init();
        $l = \atk4\ui\Lister::addTo($v, ['defaultTemplate' => 'lister.html']);
        $l->setSource(['foo', 'bar']);
    }

    /**
     * Or clone lister's template from parent.
     */
    public function testListerRender2()
    {
        $v = new \atk4\ui\View(['template' => new \atk4\ui\Template('hello{list}, world{/list}')]);
        $v->init();
        $l = \atk4\ui\Lister::addTo($v, [], ['list']);
        $l->setSource(['foo', 'bar']);
        $this->assertSame('hello, world, world', $v->render());
    }

    public function testAddAfterRender()
    {
        $this->expectException(Exception::class);
        $v = new \atk4\ui\View();
        $v->init();
        $l = \atk4\ui\Lister::addTo($v);
        $l->setSource(['foo', 'bar']);
    }
}
