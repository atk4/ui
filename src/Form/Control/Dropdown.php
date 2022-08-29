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

    /**
     * Whether or not this dropdown required a value.
     *  when set to true, $empty is shown on page load
     *  but is not selectable once a value has been choosen.
     *
     * @var bool
     */
    public $isValueRequired = false;

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

    /**
     * Default settings for Dropdown and Autocomplete Fomantic-UI components.
     */
    public static function getDefaultDropdownSettings(bool $forAutocomplete): array
    {
        $options = [
            'selectOnKeydown' => false,
            // fix: remove search term after dropdown close, needed with forceSelection = false (default since Fomantic-UI v2.9.0)
            'onHide' => new JsFunction([], [new JsExpression('$(this).dropdown(\'remove searchTerm\');')]),
            // do not force direction, otherwise the content can be shown below the actual viewport ('direction' => 'downward')
            'duration' => 100,
        ];

        if (!$forAutocomplete) {
            $options = array_merge($options, [
                'minCharacters' => 0,
                'fullTextSearch' => true, // needed for diacritics, exact is strictly exact 'exact'
                'match' => 'text',
            ]);
        }

        return $options;
    }

    protected function init(): void
    {
        parent::init();

        $this->ui = ' ';

        if ($this->entityField && $this->entityField->getField()->required) {
            $this->isValueRequired = true;
        }

        $this->dropdownOptions = static::getDefaultDropdownSettings(false);
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
        $this->dropdownOptions = $options;
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

        $this->jsDropdown(true)->dropdown($this->dropdownOptions);

        $this->template->set('DefaultText', $this->empty);

        $options = [];
        if (!$this->isValueRequired && !$this->multiple) {
            $options[] = ['div', ['class' => 'item', 'data-value' => ''], $this->empty || is_numeric($this->empty) ? [$this->empty] : []];
        }

        if (isset($this->model)) {
            foreach ($this->model as $key => $row) {
                $title = $row->getTitle();
                $item = ['div', ['class' => 'item', 'data-value' => (string) $key], $title || is_numeric($title) ? [(string) $title] : []];
                $options[] = $item;
            }
        } else {
            foreach ($this->values as $key => $val) {
                if (is_array($val)) {
                    if (array_key_exists('icon', $val)) {
                        $val = "<i class='{$val['icon']}'></i>{$val[0]}";
                    }
                }
                $item = ['div', ['class' => 'item', 'data-value' => (string) $key], $val || is_numeric($val) ? [(string) $val] : []];
                $options[] = $item;
            }
        }

        $items = $this->getApp()->getTag('div', [
            'class' => 'menu',
        ], $options);

        $this->template->tryDangerouslySetHtml('Items', $items);

        parent::renderView();
    }
}
