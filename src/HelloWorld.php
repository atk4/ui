<?php

namespace atk4\ui;

/**
 * Implements Hello World. Add this view anywhere!
 */
class HelloWorld extends View
{
    public function init(): void
    {
        parent::init();
        $this->set('Content', 'Hello World');
    }
}
