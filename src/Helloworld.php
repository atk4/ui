<?php

declare(strict_types=1);

namespace atk4\ui;

/**
 * Implements Hello World. Add this view anywhere!
 */
class Helloworld extends View
{
    public function init(): void
    {
        parent::init();
        $this->set('Content', 'Hello World');
    }
}
