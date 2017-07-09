<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class Checkbox extends Generic
{
    public $ui = 'checkbox';

    public $defaultTemplate = 'formfield/checkbox.html';

    public function init()
    {
        parent::init();

        // checkboxes are annoying becasue they don't send value
        // when they are not ticked. We assume they are ticked and
        // sent "false" as a workaround
        if ($this->form) {
            $this->form->addHook('loadPOST', function ($form, &$post) {
                if (!isset($post[$this->field->short_name])) {
                    $post[$this->field->short_name] = false;
                }
            });
        }
    }

    public function renderView()
    {
        $this->js(true)->checkbox();

        return parent::renderView();
    }
}
