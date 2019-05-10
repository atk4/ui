<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;
use atk4\ui\jsExpression;
use atk4\ui\jsFunction;

/**
 * Input element for a form field.
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
    public $type = null;

    /**
     * Any other options you'd like to pass to calendar JS.
     * See https://github.com/mdehoog/Semantic-UI-Calendar for all possible options.
     */
    public $options = [];

    /**
     * Allow to set Calendar.js function.
     *
     * @param $name
     * @param $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function renderView()
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
            $this->options['onShow'] = new jsFunction([new jsExpression('return false')]);
        }

        $typeFormat = $this->type.'_format';
        if ($format = $this->app->ui_persistence->$typeFormat) {
            $formatter = 'function(date, settings){
                            if (!date) return;
                            return atk.phpDate([format], date);
                        }';
            $this->options['formatter'][$this->type] = new jsExpression($formatter, ['format' => $format]);
        }

        $this->options['type'] = $this->type;

        if ($dayOfWeek = $this->app->ui_persistence->firstDayOfWeek) {
            $this->options['firstDayOfWeek'] = $dayOfWeek;
        }

        $this->js(true)->calendar($this->options);

        parent::renderView();
    }

    /**
     * Shorthand method for on('change') event.
     * Some input fields, like Calendar, could call this differently.
     *
     * If $expr is string or jsExpression, then it will execute it instantly.
     *
     * Examples:
     * $field->onChange('console.log(date, text, mode)');
     * $field->onChange(new \atk4\ui\jsExpression('console.log(date, text, mode)'));
     * $field->onChange('$(this).parents(".form").form("submit")');
     *
     * @param string|jsExpression|array $expr
     * @param bool                      $useDefault
     */
    public function onChange($expr, $useDefault = true)
    {
        if (is_string($expr)) {
            $expr = new \atk4\ui\jsExpression($expr);
        }
        if (!is_array($expr)) {
            $expr = [$expr];
        }

        $default['preventDefault'] = $useDefault;
        $default['stopPropagation'] = $useDefault;

        // Semantic-UI Calendar have different approach for on change event
        $this->options['onChange'] = new \atk4\ui\jsFunction(['date', 'text', 'mode'], $expr, $default);
    }
}
