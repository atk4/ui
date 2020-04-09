<?php

namespace atk4\ui\Persistence;

use atk4\data\Model;
use atk4\ui\Exception;

/**
 * This class is used for typecasting model types to the values that will be presented to the user. App will
 * always initialize this persistence in $app->ui_persistence and this object will be used by various
 * UI elements to output data to the user.
 *
 * Overriding and extending this class is a great place where you can tweak how various data-types are displayed
 * to the user in the way so it would affect UI globally.
 *
 * You may want to localize some of the output.
 */
class UI extends \atk4\data\Persistence
{
    public $date_format = 'M d, Y';

    public $time_format = 'H:i';

    public $datetime_format = 'M d, Y H:i:s';
    // 'D, d M Y H:i:s O';

    /**
     * Calendar input first day of week.
     *  0 = sunday;.
     *
     * @var int
     */
    public $firstDayOfWeek = 0;

    public $currency = '€';

    /**
     * Default decimal count for type 'money'
     *  Used directly in number_format() second parameter.
     *
     * @var int
     */
    public $currency_decimals = 2;

    public $yes = 'Yes';
    public $no = 'No';

    public $calendar_options = [];

    /**
     * This method contains the logic of casting generic values into user-friendly format.
     */
    public function _typecastSaveField(\atk4\data\Field $f, $value)
    {
        // serialize if we explicitly want that
        if ($f->serialize) {
            $value = $this->serializeSaveField($f, $value);
        }

        // always normalize string EOL
        if (is_string($value) && !$f->serialize) {
            $value = preg_replace('~\r?\n|\r~', "\n", $value);
        }

        // work only on copied value not real one !!!
        $value = is_object($value) ? clone $value : $value;

        switch ($f->type) {
        case 'boolean':
            $value = $value ? $this->yes : $this->no;
            break;
        case 'money':
            $value = ($this->currency ? $this->currency . ' ' : '') . number_format($value, $this->currency_decimals);
            break;
        case 'date':
        case 'datetime':
        case 'time':
            $dt_class = $f->dateTimeClass ?? \DateTime::class;
            $tz_class = $f->dateTimeZoneClass ?? \DateTimeZone::class;

            if ($value instanceof $dt_class || $value instanceof \DateTimeInterface) {
                $formats = ['date' => $this->date_format, 'datetime' => $this->datetime_format, 'time' => $this->time_format];
                $format = $f->persist_format ?: $formats[$f->type];

                // datetime only - set to persisting timezone
                if ($f->type == 'datetime' && isset($f->persist_timezone)) {
                    $value = new $dt_class($value->format('Y-m-d H:i:s.u'), $value->getTimezone());
                    $value->setTimezone(new $tz_class($f->persist_timezone));
                }
                $value = $value->format($format);
            }
            break;
        case 'array':
        case 'object':
            // don't encode if we already use some kind of serialization
            $value = $f->serialize ? $value : json_encode($value);
            break;
        }

        return $value;
    }

    /**
     * Interpret user-defined input for various types.
     */
    public function _typecastLoadField(\atk4\data\Field $f, $value)
    {
        // serialize if we explicitly want that
        if ($f->serialize && $value) {
            try {
                $new_value = $this->serializeLoadField($f, $value);
            } catch (\Exception $e) {
                throw new Exception([
                    'Value must be ' . $f->serialize,
                    'serializator'=> $f->serialize,
                    'value'       => $value,
                    'field'       => $f,
                ]);
            }
            $value = $new_value;
        }

        // always normalize string EOL
        if (is_string($value) && !$f->serialize) {
            $value = preg_replace('~\r?\n|\r~', "\n", $value);
        }

        switch ($f->type) {
        case 'string':
        case 'text':
            break;
        case 'boolean':
            $value = (bool) $value;
            break;
        case 'money':
            $value = str_replace(',', '', $value);
            break;
        case 'date':
        case 'datetime':
        case 'time':
            $dt_class = $f->dateTimeClass ?? \DateTime::class;
            $tz_class = $f->dateTimeZoneClass ?? \DateTimeZone::class;

            // ! symbol in date format is essential here to remove time part of DateTime - don't remove, this is not a bug
            $formats = ['date' => '!+' . $this->date_format, 'datetime' => '!+' . $this->datetime_format, 'time' => '!+' . $this->time_format];
            $format = $f->persist_format ?: $formats[$f->type];

            // datetime only - set from persisting timezone
            $valueStr = $value;
            if ($f->type == 'datetime' && isset($f->persist_timezone)) {
                $value = $dt_class::createFromFormat($format, $value, new $tz_class($f->persist_timezone));
                if ($value === false) {
                    throw new Exception(['Incorrectly formatted datetime', 'format' => $format, 'value' => $valueStr, 'field' => $f]);
                }
                $value->setTimeZone(new $tz_class(date_default_timezone_get()));
            } else {
                $value = $dt_class::createFromFormat($format, $value);
                if ($value === false) {
                    throw new Exception(['Incorrectly formatted date/time', 'format' => $format, 'value' => $valueStr, 'field' => $f]);
                }
            }

            break;
        }

        if (isset($f->reference)) {
            if (empty($value)) {
                $value = null;
            }
        }

        return $value;
    }

    /**
     * This is override of the default Persistence logic to tweak the behaviour:.
     *
     *  - "actual" property is ignored
     *  - any validation for the "saving" or output is ignored.
     *  - handling of all sorts of expressions is disabled
     */
    public function typecastSaveRow(Model $m, $row)
    {
        if (!$row) {
            return $row;
        }

        $result = [];
        foreach ($row as $key => $value) {

            // Look up field object
            $f = $m->hasField($key);

            // Figure out the name of the destination field
            $field = $key;

            // We have no knowledge of the field, it wasn't defined, so
            // we will leave it as-is.
            if (!$f) {
                $result[$field] = $value;
                continue;
            }

            $value = $this->typecastSaveField($f, $value);

            // store converted value
            $result[$field] = $value;
        }

        return $result;
    }
}
