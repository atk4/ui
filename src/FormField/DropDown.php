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
    public $empty = "\u{00a0}"; // Unicode NBSP

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
     * Here a custom function for creating the html of each dropdown option
     * can be defined. The function gets each row of the model/values property as first parameter.
     * if used with $values property, gets the key of this element as second parameter.
     * When using with a model, the second parameter is null and can be ignored.
     * Must return an array with at least 'value' and 'caption' elements set.
     * Use additional 'icon' element to add an icon to this row.
     *
     * Example 1 with Model: Title in Uppercase
     * function($row) {
     *     return [
     *         'value' => $row->id,
     *         'title' => strtoupper($row->getTitle()),
     *     ];
     *  }
     *
     * Example 2 with Model: Add an icon
     * function($row) {
     *     return [
     *         'value'   => $row->id,
     *         'title'   => $row->getTitle(),
     *         'icon'    => $row->get('amount') > 1000 ? 'money' : '',
     *     ];
     * }
     *
     * Example 3 with Model: Combine Title from model fields
     * function($row) {
     *     return [
     *         'value'   => $row->id,
     *         'title'   => $row->getTitle().' ('.$row->get('title2').')',
     *     ];
     * }
     *
     * Example 4 with $values property Array:
     * function($value, $key) {
     *     return [
     *        'value' => $key,
     *        'title' => strtoupper($value),
     *        'icon'  => strpos('Month', $value) !== false ? 'calendar' : '',
     *     ];
     * }
     *
     * @var callable
     */
    public $renderRowFunction;

    /**
     * Subtemplate for a single dropdown item.
     *
     * @var object
     */
    protected $_tItem;

    /**
     * Subtemplate for an icon for a single dropdown item.
     *
     * @var object;
     */
    protected $_tIcon;

    /**
     * Initialization.
     */
    public function init(): void
    {
        parent::init();

        $this->ui = ' ';
        $this->inputType = 'hidden';

        $this->_tItem = $this->template->cloneRegion('Item');
        $this->template->del('Item');
        $this->_tIcon = $this->_tItem->cloneRegion('Icon');
        $this->_tItem->del('Icon');
    }

    /**
     * returns <input .../> tag.
     *
     * @return string
     */
    public function getInput()
    {
        return $this->app->getTag('input', array_merge([
            'name'        => $this->short_name,
            'type'        => $this->inputType,
            'id'          => $this->id . '_input',
            'value'       => $this->getValue(),
            'readonly'    => $this->readonly ? 'readonly' : false,
            'disabled'    => $this->disabled ? 'disabled' : false,
        ], $this->inputAttr));
    }

    /**
     * Returns presentable value to be inserted into input tag.
     *
     * DropDown input tag accepts only CSV formatted list of IDs.
     *
     * @return mixed
     */
    public function getValue()
    {
        return isset($this->field)
            ? (is_array($this->field->get()) ? join(',', $this->field->get()) : $this->field->get())
            : parent::getValue();
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
            if ($this->field->type == 'array' && is_string($value)) {
                $value = explode(',', $value);
            }
            $this->field->set($value);

            return $this;
        }

        return parent::set($value, $junk);
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
        $this->dropdownOptions = array_merge($this->dropdownOptions, $options);
    }

    /**
     * Render js for dropdown.
     *
     * @return mixed
     * @throws \atk4\core\Exception
     */
    protected function jsRenderDropdown()
    {
        return $this->js(true)->dropdown($this->dropdownOptions);
    }

    /**
     * Render values as html for DropDown.
     * 
     */
    protected function htmlRenderValue()
    {
        //add selection only if no value is required and Dropdown has no multiple selections enabled
        if ($this->field !== null && !$this->field->required && !$this->isMultiple) {
            $this->_tItem->set('value', '');
            $this->_tItem->set('title', $this->empty || is_numeric($this->empty) ? (string) $this->empty : '');
            $this->template->appendHTML('Item', $this->_tItem->render());
        }

        //model set? use this, else values property
        if (isset($this->model)) {
            if (!is_callable($this->renderRowFunction)) {
                //for standard model rendering, only load id and title field
                $this->model->only_fields = [$this->model->title_field, $this->model->id_field];
                $this->_renderItemsForModel();
            } else {
                foreach ($this->model as $row) {
                    $this->_addCallBackRow($row);
                }
            }
        } else {
            if (!is_callable($this->renderRowFunction)) {
                $this->_renderItemsForValues();
            } else {
                foreach ($this->values as $key => $value) {
                    $this->_addCallBackRow($value, $key);
                }
            }
        }
    }

    /**
     * Renders view.
     */
    public function renderView()
    {
        if ($this->isMultiple) {
            $this->defaultClass = $this->defaultClass . ' multiple';
        }

        $this->addClass($this->defaultClass);

        if ($this->readonly || $this->disabled) {
            $this->setDropdownOption('showOnFocus', false);
            $this->setDropdownOption('allowTab', false);
            $this->removeClass('search');
        }

        if ($this->disabled) {
            $this->addClass('disabled');
        }

        if ($this->readonly) {
            $this->setDropdownOption('allowTab', false);
            $this->setDropdownOption('onShow', new jsFunction([new jsExpression('return false')]));
        }


        if ($this->dropIcon) {
            $this->template->trySet('DropIcon', $this->dropIcon);
        }

        $this->template->trySet('DefaultText', $this->empty);


        $this->htmlRenderValue();
        $this->jsRenderDropdown();

        parent::renderView();
    }

    /*
     * Sets the dropdown items to the template if a model is used
     */
    protected function _renderItemsForModel()
    {
        foreach ($this->model as $key => $row) {
            $title = $row->getTitle();
            $this->_tItem->set('value', (string) $key);
            $this->_tItem->set('title', $title || is_numeric($title) ? (string) $title : '');
            //add item to template
            $this->template->appendHTML('Item', $this->_tItem->render());
        }
    }

    /*
     * sets the dropdown items from $this->values array
     */
    protected function _renderItemsForValues()
    {
        foreach ($this->values as $key => $val) {
            $this->_tItem->set('value', (string) $key);
            if (is_array($val)) {
                if (array_key_exists('icon', $val)) {
                    $this->_tIcon->set('icon', $val['icon']);
                    $this->_tItem->setHTML('Icon', $this->_tIcon->render());
                } else {
                    $this->_tItem->del('Icon');
                }
                $this->_tItem->set('title', $val[0] || is_numeric($val[0]) ? (string) $val[0] : '');
            } else {
                $this->_tItem->set('title', $val || is_numeric($val) ? (string) $val : '');
            }

            //add item to template
            $this->template->appendHTML('Item', $this->_tItem->render());
        }
    }

    /*
     * used when a custom callback is defined for row rendering. Sets
     * values to row tempalte and appends it to main template
     */
    protected function _addCallBackRow($row, $key = null)
    {
        $res = call_user_func($this->renderRowFunction, $row, $key);
        $this->_tItem->set('value', (string) $res['value']);
        $this->_tItem->set('title', $res['title']);

        //Icon
        $this->_tItem->del('Icon');
        if (isset($res['icon'])
        && $res['icon']) {
            //compatibility with how $values property works on icons: 'icon'
            //is defined in there
            $this->_tIcon->set('icon', 'icon ' . $res['icon']);
            $this->_tItem->appendHTML('Icon', $this->_tIcon->render());
        }

        //add item to template
        $this->template->appendHTML('Item', $this->_tItem->render());
    }
}
