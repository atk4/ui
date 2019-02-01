<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;
use atk4\ui\jsExpression;
use atk4\ui\jsFunction;

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

    /**
     * When using a model, here a custom function for creating the html
     * of each dropdown option can be defined. The function gets each
     * record of the model as parameter.
     * Has to return array of format:
     * ['htmlelement', 'class' => 'cssclasses', 'data-value' => 'someid', ['sometitle']]
     * e.g. ['div', 'class' => 'item', 'data-value' => (string) $model->get($model->id_field), [$model->get($model->title_field)]]
     * also compare to renderView() function
     *
     * Can be used to add Icon for example:
     * function($row) {
     *     $icon = $row->get('role') === 'admin' ? 'user cog' : 'user';
     *     return ['div', 'class' => 'item', 'data-value' => (string) $model->get($model->id_field), ['<i class="'.$icon.'"></i>'.$model->get($model->title_field)]];
     * }
     *
     * @var callable
     */
    public $renderRowFunction;

    /**
     * Initialization.
     */
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
     *
     * @return string
     */
    public function getInput()
    {
        //fix for https://github.com/atk4/ui/issues/618
        if (isset($this->field)) {
            $value = $this->app->ui_persistence->typecastSaveField($this->field, $this->field->get());
        } else {
            $value = $this->content ?: '';
        }

        return $this->app->getTag('input', [
            'name'        => $this->short_name,
            'type'        => $this->inputType,
            'id'          => $this->id.'_input',
            'value'       => $value,
            'readonly'    => $this->readonly ? 'readonly' : false,
            'disabled'    => $this->disabled ? 'disabled' : false,
        ]);
    }

    /**
     * Set js dropdown() specific option;.
     *
     * @param string $option
     * @param mixed  $value
     */
    public function setDropdownOption($option, $value)
    {
        $this->dropdownOptions[$option] = $value;
    }

    /**
     * Set js dropdown() options.
     *
     * @param array $options
     */
    public function setDropdownOptions($options)
    {
        $this->dropdownOptions = $options;
    }

    /**
     * Renders view.
     */
    public function renderView()
    {
        if ($this->isMultiple) {
            $this->defaultClass = $this->defaultClass.' multiple';
        }

        $this->addClass($this->defaultClass);

        if ($this->readonly || $this->disabled) {
            $this->setDropdownOption('showOnFocus', false);
            $this->setDropdownOption('allowTab', false);
            $this->removeClass('search');
        }

        if ($this->readonly) {
            $this->setDropdownOption('allowTab', false);
            $this->setDropdownOption('onShow', new jsFunction([new jsExpression('return false')]));
        }

        $this->js(true)->dropdown($this->dropdownOptions);

        if ($this->dropIcon) {
            $this->template->trySet('DropIcon', $this->dropIcon);
        }

        $this->template->trySet('DefaultText', $this->empty);

        $options = [];
        if (!$this->isValueRequired && !$this->isMultiple) {
            $options[] = ['div',  'class' => 'item', 'data-value' => '', $this->empty || is_numeric($this->empty) ? [(string) $this->empty] : []];
        }

        if (isset($this->model)) {
            //if callback is defined, use all model fields and pass each
            //record to callback
            if (is_callable($this->renderRowFunction)) {
                foreach ($this->model as $key => $row) {
                    $options[] = call_user_func($this->renderRowFunction, $row);
                }
            }
            else {
                //clone model to use only id_field and title_field
                $m = clone $this->model;
                $m->onlyFields([$m->id_field, $m->title_field]);
                foreach ($m as $key => $row) {
                    $title = $row->getTitle();
                    $item = ['div', 'class' => 'item', 'data-value' => (string) $key, [$title]];
                    $options[] = $item;
                }
            }
        } else {
            foreach ($this->values as $key => $val) {
                if (is_array($val)) {
                    if (array_key_exists('icon', $val)) {
                        $val = "<i class='{$val['icon']}'></i>{$val[0]}";
                    }
                }
                $item = ['div', 'class' => 'item', 'data-value' => (string) $key, $val || is_numeric($val) ? [(string) $val] : []];
                $options[] = $item;
            }
        }

        $items = $this->app->getTag('div', [
            'class'       => 'menu',
        ], $options ? [[$options]] : []);

        $this->template->trySetHtml('Items', $items);

        parent::renderView();
    }
}
