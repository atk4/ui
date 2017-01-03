<?php

namespace atk4\ui;

/**
 * Implements a form
 */

class Form extends View {

    public $ui = 'form';

    public $template = 'form.html';

    public $layout = null;

    function addField(...$args)
    {
        if (!$this->model) {
            $this->model = new \atk4\ui\misc\ProxyModel();

        }

        $this->model->addField(...$args);
    }


    function setLayout($layout) {
        $this->layout = $layout;
    }



}
