<?php

declare(strict_types=1);

namespace atk4\ui\Form\Control;

use atk4\ui\Jquery;
use atk4\ui\JsExpression;

/**
 * Date/Time picker attached to a form control.
 */
class Calendar extends Input
{
    /**
     * Set this to 'date', 'time', 'datetime'.
     */
    public $type = 'date';

    /**
     * Any other options you'd like to pass to flatpickr JS.
     * See https://flatpickr.js.org/options/ for all possible options.
     */
    public $options = [];

    /**
     * Set flatpickr option.
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    protected function renderView(): void
    {
        // set default according to type.
        switch ($this->type) {
            case 'date':
                $this->options['dateFormat'] = $this->app->ui_persistence->date_format;

                break;
            case 'time':
                $this->options['dateFormat'] = $this->app->ui_persistence->time_format;
                $this->options['enableTime'] = true;
                $this->options['noCalendar'] = true;
                $this->options['time_24hr'] = true;

                break;
            case 'datetime':
                $this->options['dateFormat'] = $this->app->ui_persistence->datetime_format;
                $this->options['enableTime'] = true;
                $this->options['time_24hr'] = true;

                break;
        }

        // override from ui persistence
        if ($options = $this->app->ui_persistence->calendar_options) {
            array_merge($this->options, $options);
        }

        if ($this->readonly) {
            $this->options['clickOpens'] = false;
        }

        $this->jsInput(true)->flatpickr($this->options);

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

        // flatpickr on change event
        $this->options['onChange'] = new \atk4\ui\JsFunction(['date', 'text', 'mode'], $expr, $default);
    }

    /**
     * Get the flatPickr instance of this input in order to
     * get it's properties like selectedDates or run it's methods.
     * Ex: clearing date via js
     *     $btn->on('click', $f->getControl('date')->jsGetFlatPickr()->clear());.
     */
    public function jsGetFlatPickr(): JsExpression
    {
        return (new Jquery('#' . $this->id . '_input'))->get(0)->_flatpickr;
    }
}
