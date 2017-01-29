<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements Hello World. Add this view anywhere!
 */

class HelloWorld extends View
{
    function init()
    {
        parent::init();
        $this->set('Content', 'Hello World');
    }
}
