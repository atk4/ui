<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Card;
use Atk4\Ui\View;

class CustomCard extends Card
{
    public function getButtonContainer()
    {
        if (!$this->btnContainer) {
            // $this->btnContainer = $this->addExtraContent(new View(['ui' => 'buttons']));
            $this->btnContainer = $this->add(new View(['ui' => 'buttons bottom attached'])); // attach buttons to bottom
            $this->getButtonContainer()->addClass('wrapping');
            if ($this->hasFluidButton) {
                $this->getButtonContainer()->addClass('fluid');
            }
        }

        return $this->btnContainer;
    }
}
