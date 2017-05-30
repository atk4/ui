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

    public $currency = '€';

    /**
     * This method contains the logic of casting generic values into user-friendly format.
     */
    public function _typecastSaveField(\atk4\data\Field $f, $value)
    {
        // work only on copied value not real one !!!
        $v = is_object($value) ? clone $value : $value;

        switch ($f->type) {
        case 'boolean':
            return $v ? 'Yes' : 'No';
        case 'money':
            return ($this->currency ? $this->currency.' ' : '').number_format($v, 2);
        case 'date':
        case 'datetime':
        case 'time':
            $dt_class = isset($f->dateTimeClass) ? $f->dateTimeClass : 'DateTime';
            $tz_class = isset($f->dateTimeZoneClass) ? $f->dateTimeZoneClass : 'DateTimeZone';

            if ($v instanceof $dt_class) {
                $format = ['date' => $this->date_format, 'datetime' => $this->datetime_format, 'time' => $this->time_format];
                $format = $f->persist_format ?: $format[$f->type];

                // datetime only - set to persisting timezone
                if ($f->type == 'datetime' && isset($f->persist_timezone)) {
                    $v->setTimezone(new $tz_class($f->persist_timezone));
                }
                $v = $v->format($format);
            }
            break;
        case 'array':
        case 'object':
            // don't encode if we already use some kind of serialization
            $v = $f->serialize ? $v : json_encode($v);
            break;
        }

        return $v;
    }

    /**
     * Interpret user-defined input for various types.
     */
    public function _typecastLoadField(\atk4\data\Field $f, $value)
    {
        switch ($f->type) {
        case 'string':
        case 'text':
            // Normalize line breaks
            $value = str_replace(["\r\n", "\r"], "\n", $value);
            break;
        case 'boolean':
            $value = (bool) $value;
            break;
        case 'money':
            return str_replace(',', '', $value);
        case 'date':
        case 'datetime':
        case 'time':
            $dt_class = isset($f->dateTimeClass) ? $f->dateTimeClass : 'DateTime';
            $tz_class = isset($f->dateTimeZoneClass) ? $f->dateTimeZoneClass : 'DateTimeZone';

            // ! symbol in date format is essential here to remove time part of DateTime - don't remove, this is not a bug
            $format = ['date' => '!+'.$this->date_format, 'datetime' => '!+'.$this->datetime_format, 'time' => '!+'.$this->time_format];
            $format = $f->persist_format ?: $format[$f->type];

            // datetime only - set from persisting timezone
            if ($f->type == 'datetime' && isset($f->persist_timezone)) {
                $v = $dt_class::createFromFormat($format, $value, new $tz_class($f->persist_timezone));
                if ($v === false) {
                    throw new Exception(['Incorrectly formatted datetime', 'format' => $format, 'value' => $value, 'field' => $f]);
                }
                $v->setTimeZone(new $tz_class(date_default_timezone_get()));

                return $v;
            } else {
                $v = $dt_class::createFromFormat($format, $value);
                if ($v === false) {
                    throw new Exception(['Incorrectly formatted date/time', 'format' => $format, 'value' => $value, 'field' => $f]);
                }

                return $v;
            }

            break;
        }

        if (isset($f->reference)) {
            if ($value === '') {
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
            $f = $m->hasElement($key);

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
