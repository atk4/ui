<?php

namespace atk4\ui\tests;

use atk4\ui\Button;
use atk4\ui\Buttons;
use atk4\ui\H2;
use atk4\ui\Icon;
use atk4\ui\Label;
use atk4\ui\Template;
use atk4\ui\View;

class ButtonTest extends \atk4\core\PHPUnit_AgileTestCase
{
    /**
     * Test constructor.
     */
    public function testButtonIcon()
    {
        $b = new Button(['Load', 'icon'=>'pause']);
        $b->render();
    }

}
