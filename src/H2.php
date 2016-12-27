<?php

namespace atk4\ui;

class H2 extends View
{
    public function init()
    {
        parent::init();
        // TEMPORARY IMPLEMENTATION
        $this->template->set('_element', 'h4');
    }
}
