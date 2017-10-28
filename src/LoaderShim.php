<?php

namespace atk4\ui;

class LoaderShim extends View
{
    public $ui = 'padded inverted red segment';

    public $minHeigh = '7em';

    function init() {
        parent::init();

        if ($this->minHeigh) {
            $this->addStyle('min-height', $this->minHeigh);
        }
    }
}
