<?php

namespace atk4\ui\tests;

use atk4\ui\Button;
use atk4\ui\Icon;

class ButtonTest extends \atk4\core\PHPUnit_AgileTestCase
{
    /**
     * Test constructor.
     */
    public function testButtonIcon()
    {
        $b = new Button(['Load', 'icon' => 'pause']);
        $b->render();
    }
}
