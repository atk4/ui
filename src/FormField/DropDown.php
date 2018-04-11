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
        //See https://github.com/atk4/ui/issues/418
        //if field is required, disable empty selection once a value is
        //selected. Currently standard behaviour of sematic ui dropdown
        if(isset($this->field) && $this->field->required) {
            $this->jsInput(true)->dropdown();
        }
        //add any (does not have to be $this->empty) placeholder to allow
        //empty selection even after a value was selected
        else {
            $this->jsInput(true)->dropdown(['placeholder' => $this->empty]);
        }
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
                $title = $row->getTitle();

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

    public function renderView()
    {
        $this->jsInput(true)->dropdown($this->options);
        parent::renderView();
    }
}
