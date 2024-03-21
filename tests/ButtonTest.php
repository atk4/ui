<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Button;

class ButtonTest extends TestCase
{
    use CreateAppTrait;

    /**
     * @doesNotPerformAssertions
     */
    public function testButtonIcon(): void
    {
        $button = new Button(['Load', 'icon' => 'pause']);
        $button->setApp($this->createApp());
        $button->render();
    }
}
