<?php

namespace atk4\ui\tests;

use atk4\ui\View;

/**
 * Multiple tests to ensure that adding views through various patterns initializes them
 * nicely still
 */
class RenderTreeTest extends \atk4\core\PHPUnit_AgileTestCase
{
    /**
     * Test constructor.
     */
    public function testBasic()
    {
        $b = new View();
        $b->render();

        $this->assertNotNull($b->app);
        $this->assertNotNull($b->template);
    }


    public function testBasicNest1()
    {
        $b = new View();

        $b2 = $b->add(new View());

        $b->render();

        $this->assertNotNull($b2->app);
        $this->assertNotNull($b2->template);

        $this->assertSame($b2->app, $b->app);
    }


}
