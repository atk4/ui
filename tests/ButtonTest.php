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
        $b = new Button(['Load', 'icon' => 'pause']);
        $b->setApp($this->createApp());
        $b->render();
    }
}
