<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class Dropdown extends Input
{
    public $rows = 2;

    public $values = [];

    public function init()
    {
        parent::init();
        $this->jsInput(true)->dropdown();
    }

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        $value = isset($this->field) ? $this->app->ui_persistence->typecastSaveField($this->field, $this->field->get()) : $this->content ?: '';

        $options = [];
        foreach ($this->values as $key=>$val) {
            $item = ['option', 'value'=>(string) $key, $val];
            if ($value == $val) {
                $item['selected'] = true;
            }
            $options[] = $item;
        }

        return $this->app->getTag('select', [
            'name'       => $this->short_name,
            'type'       => $this->inputType,
            'rows'       => $this->rows,
            'placeholder'=> $this->placeholder,
            'id'         => $this->id.'_input',
        ], [$options]
       //
    );
        //return '<input name="'.$this->short_name.'" type="'.$this->inputType.'" placeholder="'.$this->placeholder.'" id="'.$this->id.'_input"/>';
    }
}
