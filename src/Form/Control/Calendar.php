<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;

class Calendar extends Input
{
    public string $inputType = 'text';

    /**
     * @var 'date'|'time'|'datetime'
     */
    public string $type = 'date';

    /**
     * Any other options you'd like to pass to Flatpickr JS.
     * See https://flatpickr.js.org/options/ for all possible options.
     */
    public array $options = [];

    /**
     * Set Flatpickr option.
     *
     * @param mixed $value
     */
    public function setOption(string $name, $value): void
    {
        $this->options[$name] = $value;
    }

    protected function init(): void
    {
        parent::init();

        $this->options['allowInput'] ??= true;

        // setup format
        $phpFormat = $this->getApp()->uiPersistence->{$this->type . 'Format'};
        $this->options['dateFormat'] = $this->convertPhpDtFormatToFlatpickr($phpFormat, true);
        if ($this->type === 'datetime' || $this->type === 'time') {
            $this->options['noCalendar'] = $this->type === 'time';
            $this->options['enableTime'] = true;
            $this->options['time_24hr'] = $this->isDtFormatWith24hrTime($phpFormat);
            $this->options['enableSeconds'] ??= $this->isDtFormatWithSeconds($phpFormat);
            $this->options['formatSecondsPrecision'] ??= $this->isDtFormatWithMicroseconds($phpFormat) ? 6 : -1;
            $this->options['disableMobile'] = true;
            if (!$this->options['enableSeconds']) {
                $this->options['formatDate'] = new JsFunction(
                    ['date', 'format', 'locale', 'formatSecondsPrecision'],
                    [new JsExpression('return flatpickr.formatDate(date, format, locale, formatSecondsPrecision).replace(/: ?0+(?! ?\.)(?=(?: |$))/, \'\');')]
                );
            }
        }

        // setup locale
        $this->options['locale'] = [
            'firstDayOfWeek' => $this->getApp()->uiPersistence->firstDayOfWeek,
        ];
    }

    protected function renderView(): void
    {
        if ($this->readOnly) {
            $this->options['clickOpens'] = false;
        }

        $this->jsInput(true)->flatpickr($this->options);

        parent::renderView();
    }

    /**
     * @param JsExpressionable $expr
     */
    public function onChange($expr, $default = []): void
    {
        if (!$expr instanceof JsBlock) {
            $expr = [$expr];
        }

        $this->options['onChange'] = new JsFunction(['date', 'text', 'mode'], $expr);
    }

    /**
     * Get the FlatPickr instance of this input in order to
     * get it's properties like selectedDates or run it's methods.
     * Ex: clearing date via JS
     *     $button->on('click', $f->getControl('date')->getJsInstance()->clear());.
     *
     * @return JsChain
     */
    public function getJsInstance(): JsExpressionable
    {
        return (new Jquery('#' . $this->name . '_input'))->get(0)->_flatpickr;
    }

    private function expandPhpDtFormat(string $phpFormat): string
    {
        $phpFormat = str_replace('c', \DateTimeInterface::ISO8601, $phpFormat);
        $phpFormat = str_replace('r', \DateTimeInterface::RFC2822, $phpFormat);

        return $phpFormat;
    }

    public function convertPhpDtFormatToFlatpickr(string $phpFormat, bool $enforceSeconds): string
    {
        if ($enforceSeconds) {
            $phpFormat = preg_replace('~: ?i\K(?!\w| ?: ?s)~', ':s', $phpFormat);
        }

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
