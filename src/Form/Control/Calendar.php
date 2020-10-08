<?php

declare(strict_types=1);

namespace atk4\ui\Form\Control;

use atk4\ui\App;
use atk4\ui\Jquery;
use atk4\ui\JsChain;
use atk4\ui\JsExpression;
use atk4\ui\Persistence\Type\Date;

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

    /**
     * Set first day of week globally.
     */
    public static function setFirstDayOfWeek(App $app, int $day)
    {
        $app->html->js(true, (new JsExpression('flatpickr.l10ns.default.firstDayOfWeek = [day]', ['day' => $day])));
    }

    /**
     * Load flatpickr locale file.
     * Pass it has an option when adding Calendar input.
     *  Form\Control\Calendar::requireLocale($app, 'fr');
     *  $form->getControl('date')->options['locale'] = 'fr';.
     */
    public static function requireLocale(App $app, string $locale)
    {
        $app->requireJs($app->cdn['flatpickr'] . '/l10n/' . $locale . '.js');
    }

    /**
     * Apply locale globally to all flatpickr instance.
     */
    public static function setLocale(App $app, string $locale)
    {
        self::requireLocale($app, $locale);
        $app->html->js(true, (new JsChain('flatpickr'))->localize((new JsChain('flatpickr'))->l10ns->{$locale}));
    }

    /**
     * Set first day of week for calendar display.
     * Applied globally to all flatpickr instance.
     */
    public static function setDayOfWeek(App $app, int $day)
    {
        $app->html->js(true, (new JsExpression('flatpickr.l10ns.default.firstDayOfWeek = [day]', ['day' => $day])));
    }

    protected function init(): void
    {
        parent::init();

        // get format from Persistence\Date.
        $format = $this->translateFormat(Date::getProps($this->type));
        $this->options['dateFormat'] = $format;

        if ($this->type === 'datetime' || $this->type === 'time') {
            $this->options['enableTime'] = true;
            $this->options['time_24hr'] = $this->options['time_24hr'] ?? $this->use24hrTimeFormat();
            $this->options['noCalendar'] = $this->type === 'time' ? true : false;

            // Add seconds picker if set
            $this->options['enableSeconds'] = $this->options['enableSeconds'] ?? $this->useSeconds();

            // Allow edit if microseconds or second is set.
            $this->options['allowInput'] = $this->options['allowInput'] ?? $this->allowMicroSecondsInput();
        }
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

    private function translateFormat(string $format): string
    {
        // translate from php to flatpickr.
        $format = preg_replace(['~[aA]~', '~[s]~', '~[g]~'], ['K', 'S', 'G'], $format);

        return $format;
    }

    private function use24hrTimeFormat(): bool
    {
        return !(bool) preg_match('~[gGh]~', $this->options['altFormat'] ?? $this->options['dateFormat']);
    }

    private function useSeconds(): bool
    {
        return (bool) preg_match('~[S]~', $this->options['altFormat'] ?? $this->options['dateFormat']);
    }

    private function allowMicroSecondsInput(): bool
    {
        return (bool) preg_match('~[u]~', $this->options['altFormat'] ?? $this->options['dateFormat']);
    }
}
