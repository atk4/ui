<?php

namespace atk4\ui;

class Buttons extends View
{
    // @inheritdoc
    public $_class = 'buttons';

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        if ($this->content) {
            $this->addClass($this->content);
            $this->content = false;
        }
        parent::renderView();
    }
}
