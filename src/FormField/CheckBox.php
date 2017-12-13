<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class CheckBox extends Generic
{
    public $ui = 'checkbox';

    public $defaultTemplate = 'formfield/checkbox.html';

    public $label;

    public function __construct($label = null, $class = null)
    {
        parent::__construct($label, $class);

        // in constructor we provide label not content (value of field)
        $this->label = $this->content;
        $this->content = null;
    }

    public function init()
    {
        parent::init();

        // checkboxes are annoying because they don't send value
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
        // if no value, then remove "selected" attribute
        if (!$this->getValue()) {
            $this->template->tryDel("checked");
        }
        if ($this->label) {
            $this->template->trySet('Label', $this->label);
        }

        $this->js(true)->checkbox();

        $this->content = null; // no content again
        return parent::renderView();
    }
}
