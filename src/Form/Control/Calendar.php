<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Jquery;
use Atk4\Ui\JsExpression;

/**
 * Date/Time picker attached to a form control.
 */
class Calendar extends Input
{
    /**
     * Set this to 'date', 'time', 'datetime'.
     */
    public string $type = 'date';

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

    protected function init(): void
    {
        parent::init();

        // setup format
        $phpFormat = $this->getApp()->ui_persistence->{$this->type . '_format'};
        $this->options['dateFormat'] = $this->convertPhpDtFormatToFlatpickr($phpFormat);

        if ($this->type === 'datetime' || $this->type === 'time') {
            $this->options['enableTime'] = true;
            $this->options['time_24hr'] ??= $this->isDtFormatWith24hrTime($phpFormat);
            $this->options['noCalendar'] = ($this->type === 'time');
            $this->options['enableSeconds'] ??= $this->isDtFormatWithSeconds($phpFormat);
            $this->options['allowInput'] ??= $this->isDtFormatWithMicroseconds($phpFormat);
        }

        // setup locale
        $this->options['locale'] = [
            'firstDayOfWeek' => $this->getApp()->ui_persistence->firstDayOfWeek,
        ];
    }

    protected function renderView(): void
    {
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
     * $control->onChange(new \Atk4\Ui\JsExpression('console.log(date, text, mode)'));
     * $control->onChange('$(this).parents(".form").form("submit")');
     *
     * Note: Unlike Control::onChange() method, flatpickr onChange options does not use $default settings.
     *
     * @param string|JsExpression|array $expr
     * @param array|bool                $default
     */
    public function onChange($expr, $default = []): void
    {
        if (is_string($expr)) {
            $expr = new \Atk4\Ui\JsExpression($expr);
        }
        if (!is_array($expr)) {
            $expr = [$expr];
        }

        // flatpickr on change event
        $this->options['onChange'] = new \Atk4\Ui\JsFunction(['date', 'text', 'mode'], $expr);
    }

    /**
     * Get the flatPickr instance of this input in order to
     * get it's properties like selectedDates or run it's methods.
     * Ex: clearing date via js
     *     $btn->on('click', $f->getControl('date')->getJsInstance()->clear());.
     */
    public function getJsInstance(): JsExpression
    {
        return (new Jquery('#' . $this->name . '_input'))->get(0)->_flatpickr;
    }

    private function expandPhpDtFormat(string $phpFormat): string
    {
        $phpFormat = str_replace('c', \DateTimeInterface::ISO8601, $phpFormat);
        $phpFormat = str_replace('r', \DateTimeInterface::RFC2822, $phpFormat);

        return $phpFormat;
    }

    public function convertPhpDtFormatToFlatpickr(string $phpFormat): string
    {
        $res = $this->expandPhpDtFormat($phpFormat);
        foreach ([
            '~[aA]~' => 'K',
            '~[s]~' => 'S',
            '~[g]~' => 'G',
        ] as $k => $v) {
            $res = preg_replace($k, $v, $res);
        }

        return $res;
    }

    public function isDtFormatWith24hrTime(string $phpFormat): bool
    {
        return !preg_match('~[gh]~', $this->expandPhpDtFormat($phpFormat));
    }

    public function isDtFormatWithSeconds(string $phpFormat): bool
    {
        return (bool) preg_match('~[suv]~', $this->expandPhpDtFormat($phpFormat));
    }

    public function isDtFormatWithMicroseconds(string $phpFormat): bool
    {
        return (bool) preg_match('~[uv]~', $this->expandPhpDtFormat($phpFormat));
    }
}
