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
     *
     * @var string
     */
    public $label = null;

    /**
     * Constructor.
     *
     * @param string $label
     * @param string $class
     */
    public function __construct($label = null, $class = null)
    {
        $this->label = $label;

        if ($class) {
            $this->addClass($class);
        }
    }

    /**
     * Initialization.
     */
    public function init()
    {
        parent::init();

        // checkboxes are annoying because they don't send value when they are
        // not ticked. We assume they are ticked and sent boolean "false" as a
        // workaround. Otherwise send boolean "true".
        if ($this->form) {
            $this->form->addHook('loadPOST', function ($form, &$post) {
                $post[$this->field->short_name] = isset($post[$this->field->short_name]);
            });
        }
    }

    /**
     * Set field value.
     *
     * @param bool  $value
     * @param mixed $junk
     */
    public function set($value = null, $junk = null)
    {
        if (!is_bool($value)) {
            throw new Exception(['Field\CheckBox::set() needs value to be a boolean', 'value'=>$value]);
        }

        parent::set($value);
    }

    /**
     * Render view.
     */
    public function renderView()
    {
        $this->template['label'] = $this->label ?: $this->caption;

        if ($this->field ? $this->field->get() : $this->content) {
            $this->template->set('checked', 'checked');
        }

        // We don't want this displayed, because it can only affect "checked" status anyway
        $this->content = null;

        // take care of readonly status
        if ($this->readonly) {
            $this->addClass('read-only');
        }

        // take care of disabled status
        if ($this->disabled) {
            $this->addClass('disabled');
            $this->template->set('disabled', 'disabled="disabled"');
        }

        $this->js(true)->checkbox();

        $this->content = null; // no content again

        return parent::renderView();
    }
}
