<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;

class Dropdown extends Input
{
    public $defaultTemplate = 'form/control/dropdown.html';

    public string $inputType = 'hidden';

    /**
     * Values need for the dropdown.
     * Note: Now possible to display icon with value in dropdown by passing the icon class with your values.
     * ex: 'values' => [
     *     'tag' => ['Tag', 'icon' => 'tag'],
     *     'globe' => ['Globe', 'icon' => 'globe'],
     *     'registered' => ['Registered', 'icon' => 'registered'],
     *     'file' => ['File', 'icon' => 'file'],
     * ].
     *
     * @var array<int|string, mixed>
     */
    public array $values;

    /** @var string The string to set as an empty values. */
    public $empty = "\u{00a0}"; // Unicode NBSP

    /** @var array Dropdown options as per Fomantic-UI dropdown options. */
    public $dropdownOptions = [];

    /**
     * Whether or not to accept multiple value.
     *   Multiple values are sent using a string with comma as value delimiter.
     *   ex: 'value1,value2,value3'.
     *
     * @var bool
     */
    public $multiple = false;

    /**
     * Here a custom function for creating the HTML of each dropdown option
     * can be defined. The function gets each row of the model/values property as first parameter.
     * if used with $values property, gets the key of this element as second parameter.
     * When using with a model, the second parameter is null and can be ignored.
     * Must return an array with at least 'value' and 'caption' elements set.
     * Use additional 'icon' element to add an icon to this row.
     *
     * Example 1 with Model: Title in Uppercase
     * function (Model $row) {
     *     return [
     *         'value' => $row->getId(),
     *         'title' => mb_strtoupper($row->getTitle()),
     *     ];
     *  }
     *
     * Example 2 with Model: Add an icon
     * function (Model $row) {
     *     return [
     *         'value' => $row->getId(),
     *         'title' => $row->getTitle(),
     *         'icon' => $row->get('amount') > 1000 ? 'money' : '',
     *     ];
     * }
     *
     * Example 3 with Model: Combine Title from model fields
     * function (Model $row) {
     *     return [
     *         'value' => $row->getId(),
     *         'title' => $row->getTitle() . ' (' . $row->get('title2') . ')',
     *     ];
     * }
     *
     * Example 4 with $values property Array:
     * function (string $value, $key) {
     *     return [
     *        'value' => $key,
     *        'title' => mb_strtoupper($value),
     *        'icon' => str_contains($value, 'Month') ? 'calendar' : '',
     *     ];
     * }
     *
     * @var \Closure(mixed, int|string|null): array{value: mixed, title: mixed, icon?: mixed}|null
     */
    public $renderRowFunction;

    /** @var HtmlTemplate Subtemplate for a single dropdown item. */
    protected $_tItem;

    /** @var HtmlTemplate Subtemplate for an icon for a single dropdown item. */
    protected $_tIcon;

    protected function init(): void
    {
        parent::init();

        $this->_tItem = $this->template->cloneRegion('Item');
        $this->template->del('Item');
        $this->_tIcon = $this->_tItem->cloneRegion('Icon');
        $this->_tItem->del('Icon');
    }

    /**
     * Returns presentable value to be inserted into input tag.
     *
     * Dropdown input tag accepts only CSV formatted list of IDs.
     */
    public function getValue()
    {
        return $this->entityField !== null
            ? (is_array($this->entityField->get()) ? implode(', ', $this->entityField->get()) : $this->entityField->get())
            : parent::getValue();
    }

    /**
     * Sets the value of this field. If field is a part of the form and is associated with
     * the model, then the model's value will also be affected.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function set($value = null)
    {
        if ($this->entityField) {
            if ($this->entityField->getField()->type === 'json' && is_string($value)) {
                $value = explode(',', $value);
            }
            $this->entityField->set($value);

            return $this;
        }

        return parent::set($value);
    }

    /**
     * Set JS dropdown() specific option;.
     *
     * @param string $option
     * @param mixed  $value
     */
    public function setDropdownOption($option, $value): void
    {
        $this->dropdownOptions[$option] = $value;
    }

    /**
     * Set JS dropdown() options.
     *
     * @param array $options
     */
    public function setDropdownOptions($options): void
    {
        $this->dropdownOptions = array_merge($this->dropdownOptions, $options);
    }

    /**
     * @param bool|string      $when
     * @param JsExpressionable $action
     *
     * @return Jquery
     */
    protected function jsDropdown($when = false, $action = null): JsExpressionable
    {
        return $this->js($when, $action, 'div.ui.dropdown:has(> #' . $this->name . '_input)');
    }

    protected function jsRenderDropdown(): JsExpressionable
    {
        return $this->jsDropdown(true)->dropdown($this->dropdownOptions);
    }

    protected function htmlRenderValue(): void
    {
        // add selection only if no value is required and Dropdown has no multiple selections enabled
        if ($this->entityField !== null && !$this->entityField->getField()->required && !$this->multiple) {
            $this->_tItem->set('value', '');
            $this->_tItem->set('title', $this->empty);
            $this->template->dangerouslyAppendHtml('Item', $this->_tItem->renderToHtml());
        }

        // model set? use this, else values property
        if ($this->model !== null) {
            if ($this->renderRowFunction) {
                foreach ($this->model as $row) {
                    $this->_addCallBackRow($row);
                }
            } else {
                // for standard model rendering, only load ID and title field
                $this->model->setOnlyFields([$this->model->titleField, $this->model->idField]);
                $this->_renderItemsForModel();
            }
        } else {
            if ($this->renderRowFunction) {
                foreach ($this->values as $key => $value) {
                    $this->_addCallBackRow($value, $key);
                }
            } else {
                $this->_renderItemsForValues();
            }
        }
    }

    protected function renderView(): void
    {
        if ($this->multiple) {
            $this->template->dangerouslySetHtml('multipleClass', 'multiple');
        }

        if ($this->readOnly || $this->disabled) {
            if ($this->multiple) {
                $this->jsDropdown(true)->find('a i.delete.icon')->attr('class', 'disabled');
            }
        }

        if ($this->disabled) {
            $this->template->set('disabledClass', 'disabled');
            $this->template->dangerouslySetHtml('disabled', 'disabled="disabled"');
        } elseif ($this->readOnly) {
            $this->template->set('disabledClass', 'read-only');
            $this->template->dangerouslySetHtml('disabled', 'readonly="readonly"');

            $this->setDropdownOption('onShow', new JsFunction([], [new JsExpression('return false')]));
        }

        $this->template->set('DefaultText', $this->empty);

        $this->htmlRenderValue();
        $this->jsRenderDropdown();

        parent::renderView();
    }

    /**
     * Sets the dropdown items to the template if a model is used.
     */
    protected function _renderItemsForModel(): void
    {
        foreach ($this->model as $key => $row) {
            $title = $row->getTitle();
            $this->_tItem->set('value', (string) $key);
            $this->_tItem->set('title', $title || is_numeric($title) ? (string) $title : '');
            // add item to template
            $this->template->dangerouslyAppendHtml('Item', $this->_tItem->renderToHtml());
        }
    }

    /**
     * Sets the dropdown items from $this->values array.
     */
    protected function _renderItemsForValues(): void
    {
        foreach ($this->values as $key => $val) {
            $this->_tItem->set('value', (string) $key);
            if (is_array($val)) {
                if (array_key_exists('icon', $val)) {
                    $this->_tIcon->set('iconClass', $val['icon'] . ' icon');
                    $this->_tItem->dangerouslySetHtml('Icon', $this->_tIcon->renderToHtml());
                } else {
                    $this->_tItem->del('Icon');
                }
                $this->_tItem->set('title', $val[0] || is_numeric($val[0]) ? (string) $val[0] : '');
            } else {
                $this->_tItem->set('title', $val || is_numeric($val) ? (string) $val : '');
            }

            // add item to template
            $this->template->dangerouslyAppendHtml('Item', $this->_tItem->renderToHtml());
        }
    }

    /**
     * Used when a custom callback is defined for row rendering. Sets
     * values to row template and appends it to main template.
     *
     * @param mixed      $row
     * @param int|string $key
     */
    protected function _addCallBackRow($row, $key = null): void
    {
        $res = ($this->renderRowFunction)($row, $key);
        $this->_tItem->set('value', (string) $res['value']);
        $this->_tItem->set('title', $res['title']);

        $this->_tItem->del('Icon');
        if (isset($res['icon']) && $res['icon']) {
            // compatibility with how $values property works on icons: 'icon'
            // is defined in there
            $this->_tIcon->set('iconClass', 'icon ' . $res['icon']);
            $this->_tItem->dangerouslyAppendHtml('Icon', $this->_tIcon->renderToHtml());
        }

        // add item to template
        $this->template->dangerouslyAppendHtml('Item', $this->_tItem->renderToHtml());
    }
}
