<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;
use Atk4\Ui\View;

/**
 * Multiple tests to ensure that adding views through various patterns initializes them
 * nicely still.
 */
class RenderTreeTest extends AtkPhpunit\TestCase
{
    /**
     * Test constructor.
     */
    public function testBasic()
    {
        $b = new View();
        $b->render();

        $this->assertNotNull($b->getApp());
        $this->assertNotNull($b->template);
    }

    public function testBasicNest1()
    {
        $b = new View();

        $b2 = View::addTo($b);

        $b->render();

        $this->assertNotNull($b2->getApp());
        $this->assertNotNull($b2->template);

        $this->assertSame($b2->getApp(), $b->getApp());
    }
}
