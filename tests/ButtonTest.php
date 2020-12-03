<?php

declare(strict_types=1);

namespace atk4\ui\Tests;

use Atk4\Core\AtkPhpunit;
use atk4\ui\Button;

class ButtonTest extends AtkPhpunit\TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testButtonIcon()
    {
        $b = new Button(['Load', 'icon' => 'pause']);
        $b->render();
    }
}
