<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Implements Hello World. Add this view anywhere!
 */
class HelloWorld extends View
{
    protected function init(): void
    {
        parent::init();

        $this->set('Hello World');
    }
}
