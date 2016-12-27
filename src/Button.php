<?php

namespace atk4\ui;

class Button extends View
{
    public $ui = 'button';

    public $icon = null;

    /**
     * $icon property will end up a button icon.
     */
    function renderView() {
        if ($this->icon && !is_object($this->icon)) {

            $this->icon = $this->add(new Icon($this->icon), 'Content');

            if ($this->content) {
                $this->addClass('labeled');
                $this->add(new Text($this->content));
                $this->content = false;
            }

            $this->addClass('icon');
        }

        parent::renderView();
    }

    /**
     * By Default buttons should have something written on them, e.g. "Button".
     */
    public function recursiveRender()
    {
        parent::recursiveRender();
        if (!$this->template->get('Content')) {
            $this->template->set('Content', 'Button');
        }
    }
}
