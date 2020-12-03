<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;
use Atk4\Core\Exception;

class ViewTest extends AtkPhpunit\TestCase
{
    /**
     * Test redering multiple times.
     */
    public function testMultipleRender()
    {
        $v = new \Atk4\Ui\View();
        $v->set('foo');

        $a = $v->render();
        $b = $v->render();
        $this->assertSame($a, $b);
    }

    public function testAddAfterRender()
    {
        $this->expectException(Exception::class);

        $v = new \Atk4\Ui\View();
        $v->set('foo');

        $a = $v->render();
        \Atk4\Ui\View::addTo($v);  // this should fail. No adding after rendering.
        $b = $v->render();
        $this->assertSame($a, $b);
    }
}
