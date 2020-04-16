<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\ui\Button;

class ButtonTest extends AtkPhpunit\TestCase
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
