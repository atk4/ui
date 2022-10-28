<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Exception;
use Atk4\Ui\View;

class ViewTest extends TestCase
{
    public function testMultipleTimesRender(): void
    {
        $v = new View();
        $v->set('foo');

        $a = $v->render();
        $b = $v->render();
        static::assertSame($a, $b);
    }

    public function testAddAfterRender(): void
    {
        $v = new View();
        $v->set('foo');

        $v->render();

        $this->expectException(Exception::class);
        View::addTo($v); // no adding after rendering
    }

    public function testVoidTagRender(): void
    {
        $v = new View();
        static::assertSame('<div id="atk" class="  " style="" ></div>', $v->render());

        $v = new View();
        $v->element = 'img';
        static::assertSame('<img id="atk" class="  " style="" >', $v->render());
    }
}
