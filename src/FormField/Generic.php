<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;
use atk4\ui\View;

/**
 * Provides generic functionality for a form field.
 */
class Generic extends View
{
    /**
     * @var Form - to which this field belongs
     */
    public $form;

    /**
     * @var \atk4\data\Field - points to model field
     */
    public $field;

    public function init()
    {
        parent::init();
        if ($this->field->mandatory) {
            $this->form->addHook('validate', function() {
                var_dump($this->field->get());
                return [$this->field->short_name => !$this->field->get() ];
            });
        }
    }

    /**
     * It only makes sense to have "name" property inside a field if
     * it was used inside a form.
     */
    public function renderView()
    {
        if ($this->form) {
            $this->template->trySet('name', $this->short_name);
        }

        parent::renderView();
    }
}
