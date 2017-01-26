<?php

namespace atk4\ui;

class Layout extends View
{
    public function render()
    {
        if (!$this->_initialized) {
            $this->init();
        }

        $this->renderView();

        $this->recursiveRender();

        // There might be a script output tag inside our template

        if ($this->template->hasTag('HEAD')) {
            $this->template->appendHTML('HEAD', $this->getJS());

            return $this->template->render();
        } else {
            return $this->getJS().$this->template->render();
        }
    }
}
