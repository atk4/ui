<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;
use atk4\ui\Lister;

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
    public function setModel(\atk4\data\Model $m)
    {
        $this->add(new Lister(), 'Radio')->setModel($m);
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
