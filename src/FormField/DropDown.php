<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class DropDown extends Input
{
    /**
     * Values need for the dropdown.
     *  Note: Now possible to display icon with value in dropdown by passing the
     *        icon class with your values.
     * ex: 'values'  => [
     *          'tag'        => ['Tag', 'icon' => 'tag icon'],
     *          'globe'      => ['Globe', 'icon' => 'globe icon'],
     *          'registered' => ['Registered', 'icon' => 'registered icon'],
     *          'file'       => ['File', 'icon' => 'file icon']
     *          ].
     *
     *
     * @var array
     */
    public $values = [];

    /**
     * The string to set as an empty values.
     *
     * @var string
     */
    public $empty = '...';

    /**
     * Whether or not this dropdown required a value.
     *  when set to true, $empty is shown on page load
     *  but is not selectable once a value has been choosen.
     *
     * @var bool
     */
    public $isValueRequired = false;

    /**
     * The html template associate whit this dropdown.
     *
     * @var string
     */
    public $defaultTemplate = 'formfield/dropdown.html';

    /**
     * The css class associate with this dropdown.
     *
     * @var string
     */
    public $defaultClass = 'fluid search selection dropdown';

    /**
     * The icon to display at the dropdown menu.
     *  The template default is set to: 'dropdown icon'.
     *  Note: dropdown icon is show on the right side of the menu
     *  while other icon are usually display on the left side.
     *
     * @var null
     */
    public $dropIcon = null;

    /**
     * Dropdown options as per semantic-ui dropdown options.
     *
     * @var array
     */
    public $dropdownOptions = [];

    /**
     * Whether or not to accept multiple value.
     *   Multiple values are sent using a string with comma as value delimiter.
     *   ex: 'value1,value2,value3'.
     *
     * @var bool
     */
    public $isMultiple = false;

    public function init()
    {
        parent::init();

        $this->ui = ' ';
        $this->inputType = 'hidden';

        if (isset($this->field) && $this->field->required) {
            $this->isValueRequired = true;
        }
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
     * Set js dropdown() specific option;.
     *
     * @param $option
     * @param $value
     */
    public function setDropdownOption($option, $value)
    {
        $this->dropdownOptions[$option] = $value;
    }

    /**
     * Set js dropdown() options.
     *
     * @param $options
     */
    public function setDropdownOptions($options)
    {
        $this->dropdownOptions = $options;
    }

    public function renderView()
    {
        $this->js(true)->dropdown($this->dropdownOptions);

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
        if (!$this->isValueRequired && !$this->isMultiple) {
            $options[] = ['div',  'class' => 'item', 'data-value' => '', [$this->empty]];
        }

        if (isset($this->model)) {
            foreach ($this->model as $key => $row) {
                $title = $row->getTitle();
                $item = ['div', 'class' => 'item', 'data-value' => (string) $key, [$title]];
                $options[] = $item;
            }
        } else {
            foreach ($this->values as $key => $val) {
                if (is_array($val)) {
                    if (array_key_exists('icon', $val)) {
                        $val = "<i class='{$val['icon']}'></i>{$val[0]}";
                    }
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
