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

    /**
     * Label appears to the right of the checkbox. If label is not set specifically
     * then the $caption property will be displayed as a label instead.
     */
    public $label = null;

    public function __construct($label = null, $class = null)
    {
        $this->label = $label;

        if ($class) {
            $this->addClass($class);
        }
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

    public function set($value = null, $junk = null)
    {
        if (!is_bool($value)) {
            throw new Exception(['Field\CheckBox::set() needs value to be a boolean', 'value'=>$value]);
        }

        parent::set($value);
    }

    public function renderView()
    {
        $this->template['label'] = $this->label ?: $this->caption;

        if ($this->field ? $this->field->get() : $this->content) {
            $this->template->set('checked', 'checked');
        }

        /*
         * We don't want this displayed, because it can only affect "checked" status anyway
         */
        $this->content = null;

        $this->js(true)->checkbox();

        $this->content = null; // no content again
        return parent::renderView();
    }
}
