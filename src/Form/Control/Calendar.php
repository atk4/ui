<?php

declare(strict_types=1);

namespace atk4\ui\Form\Control;

use atk4\ui\JsExpression;
use atk4\ui\JsFunction;

/**
 * Input element for a form control.
 *
 * 2018-06-25 : Add Locutus js library for formatting date as per php format.
 * http://locutus.io/php/datetime/
 *
 * Locutus date function are available under atk.phpDate function.
 * ex: atk.phpDate('m.d.Y', new Date());
 */
class Calendar extends Input
{
    /**
     * Set this to 'date', 'time', 'month' or 'year'. Leaving this blank
     * will show both date and time.
     */
    public $type;

    /**
     * Any other options you'd like to pass to calendar JS.
     * See https://fomantic-ui.com/modules/calendar.html#/settings for all possible options.
     */
    public $options = [];

    /**
     * Allow to set Calendar.js function.
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    protected function renderView(): void
    {
        if (!$this->icon) {
            switch ($this->type) {
            //case 'date': $this->icon = '
            }
        }

        if (!$this->type) {
            $this->type = 'datetime';
        }

        if ($this->readonly) {
            $this->options['onShow'] = new JsFunction([new JsExpression('return false')]);
        }

        $typeFormat = $this->type . '_format';
        if ($format = $this->getApp()->ui_persistence->{$typeFormat}) {
            $formatter = 'function(date, settings){
                            if (!date) return;
                            return atk.phpDate([format], date);
                        }';
            $this->options['formatter'][$this->type] = new JsExpression($formatter, ['format' => $format]);
        }

        $this->options['type'] = $this->type;

        if ($dayOfWeek = $this->getApp()->ui_persistence->firstDayOfWeek) {
            $this->options['firstDayOfWeek'] = $dayOfWeek;
        }

        if ($options = $this->getApp()->ui_persistence->calendar_options) {
            foreach ($options as $k => $v) {
                $this->options[$k] = $v;
            }
        }

        $this->js(true)->calendar($this->options);

        parent::renderView();
    }

    /**
     * Shorthand method for on('change') event.
     * Some input fields, like Calendar, could call this differently.
     *
     * If $expr is string or JsExpression, then it will execute it instantly.
     *
     * Examples:
     * $control->onChange('console.log(date, text, mode)');
     * $control->onChange(new \atk4\ui\JsExpression('console.log(date, text, mode)'));
     * $control->onChange('$(this).parents(".form").form("submit")');
     *
     * @param string|JsExpression|array $expr
     * @param array|bool                $default
     */
    public function onChange($expr, $default = [])
    {
        if (is_string($expr)) {
            $expr = new \atk4\ui\JsExpression($expr);
        }
        if (!is_array($expr)) {
            $expr = [$expr];
        }

        if (is_bool($default)) {
            $default['preventDefault'] = $default;
            $default['stopPropagation'] = $default;
        }

        // Semantic-UI Calendar have different approach for on change event
        $this->options['onChange'] = new \atk4\ui\JsFunction(['date', 'text', 'mode'], $expr, $default);
    }
}
