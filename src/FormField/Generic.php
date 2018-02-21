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

    /**
     * @var bool - Whether you need this field to be rendered wrap in a form layout or as his.
     */
    public $layoutWrap = true;

    public $width = null;

    /**
     * Caption is a text that must appear somewhere nearby the field. For a form with layout, this
     * will typically place caption above the input field, but for checkbox this may appear next to the
     * checkbox itself. If Form Layout does not have captions above the input field, then caption
     * will appear as a placeholder of the input fields and it may also appear as a tooltip.
     *
     * Caption is usually specified by a model.
     *
     * @var string
     */
    public $caption = null;

    /**
     * Placed as a pointing label below the field. This only works when FormField appears in a form. You can also
     * set this to object, such as 'Text' otherwise HTML characters are escaped.
     *
     * @var string|\atk4\ui\View
     */
    public $hint = null;

    public function init()
    {
        parent::init();

        if ($this->form && $this->field) {
            if (isset($this->form->fields[$this->field->short_name])) {
                throw new \atk4\ui\Exception(['Form already has a field with the same name', 'name' => $this->field->short_name]);
            }
            $this->form->fields[$this->field->short_name] = $this;
        }
    }

    /**
     * Sets the value of this field. If field is a part of the form and is associated with
     * the model, then the model's value will also be affected.
     *
     * @param mixed $value
     * @param mixed $junk
     *
     * @return $this
     */
    public function set($value = null, $junk = null)
    {
        if ($this->field) {
            $this->field->set($value);

            return $this;
        }

        $this->content = $value;

        return $this;
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
