<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class DropDownPlus extends Input
{
    public $values = [];

    public $empty = '...';

    public $defaultTemplate = 'formfield/dropdown-plus.html';

    public $defaultClass = 'fluid search selection dropdown';

    public $dropIcon = null;

    public $dropOptions = [];

    public $isMultiple = false;

    public function init()
    {
        parent::init();

        $this->inputType = 'hidden';
    }

    /**
     * returns <input .../> tag.
     */
    public function getInput()
    {
        $value = isset($this->field) ? $this->app->ui_persistence->typecastSaveField($this->field, $this->field->get()) : $this->content ?: '';

        return $this->app->getTag('input', [
            'name'  => $this->short_name,
            'type'  => $this->inputType,
            'id'    => $this->id.'_input',
            'value' => $value,
        ]);
    }

    /**
     * Set js dropdown() options;.
     *
     * @param $option
     * @param $value
     */
    public function setDropdownOption($option, $value)
    {
        $this->dropOptions[$option] = $value;
    }

    public function renderView()
    {
        $this->js(true)->dropdown($this->dropOptions);

        if ($this->isMultiple) {
            $this->defaultClass = $this->defaultClass.' multiple';
            //$this->template->trySetHtml('BeforeInput', "<input name='{$inputName}' type='hidden'/>");
        }

        $this->addClass($this->defaultClass);

        if ($this->dropIcon) {
            $this->template->trySet('DropIcon', $this->dropIcon);
        }

        $this->template->trySet('DefaultText', $this->empty);

        $options = [];
        if (isset($this->model)) {
            foreach ($this->model as $key => $row) {
                $title = $row->getTitle();
                $item = ['div', 'class' => 'item', 'data-value' => (string) $key, $title];
                $options[] = $item;
            }
        } else {
            foreach ($this->values as $key => $val) {
                if (is_array($val)) {
                    $val = "<i class='{$val[1]}'></i>{$val[0]}";
                }
                $item = ['div', 'class' => 'item', 'data-value' => (string) $key, [$val]];
                $options[] = $item;
            }
        }

        $items = $this->app->getTag('div', [
            'class'       => 'menu',
        ], [[$options]]);

        $this->template->trySetHtml('Items', $items);

        parent::renderView();
    }
}
