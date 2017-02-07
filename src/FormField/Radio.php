<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class Radio extends Generic
{
    /**
     * {@inheritdoc}
     */
    public $ui = 'radio checkbox';

    /**
     * {@inheritdoc}
     */
    public $defaultTemplate = 'formfield/radio.html';

    /**
     * {@inheritdoc}
     */
    public function setModel($m)
    {
        $this->add(new \atk4\ui\Lister(), 'Radio')->setModel($m);
    }

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        $this->js(true)->checkbox();

        return parent::renderView();
    }
}
