<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class DropDown extends Input
{
    public $rows = 2;

    public $values = [];

    public $empty = '...';

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
        if ($this->empty) {
            $item = ['option', 'value' => '', $this->empty];
            $options[] = $item;
        }

        if (isset($this->model)) {
            foreach ($this->model as $key => $row) {
                $title = $row[$row->title_field];

                $item = ['option', 'value' => (string) $key, $title];
                if ($value == $key) {
                    $item['selected'] = true;
                }
                $options[] = $item;
            }
        } else {
            foreach ($this->values as $key => $val) {
                $item = ['option', 'value' => (string) $key, $val];
                if ($value == $key) {
                    $item['selected'] = true;
                }
                $options[] = $item;
            }
        }

        return $this->app->getTag('select', [
            'class'       => 'fluid search selection',
            'name'        => $this->short_name,
            'type'        => $this->inputType,
            'rows'        => $this->rows,
            'placeholder' => $this->placeholder,
            'id'          => $this->id.'_input',
        ], [[$options]]
       //
    );
        //return '<input name="'.$this->short_name.'" type="'.$this->inputType.'" placeholder="'.$this->placeholder.'" id="'.$this->id.'_input"/>';
    }
}
