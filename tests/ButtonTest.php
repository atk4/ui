<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;
use Atk4\Ui\Button;

class ButtonTest extends AtkPhpunit\TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testButtonIcon(): void
    {
        $b = new Button(['Load', 'icon' => 'pause']);
        $b->render();
    }
}
