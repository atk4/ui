<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class Radio extends Generic
{
    /**
     * @inheritDoc
     */
    public $ui = 'radio checkbox';

    /**
     * @inheritDoc
     */
    public $defaultTemplate = 'formfield/radio.html';

    /**
     * @inheritDoc
     */
    public function setModel($m)
    {
        $this->add(new \atk4\ui\Lister(), 'Radio')->setModel($m);
    }

    /**
     * @inheritDoc
     */
    public function renderView()
    {
        $this->js(true)->checkbox();

        return parent::renderView();
    }
}
