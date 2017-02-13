<?php

namespace atk4\ui\Persistence;

use atk4\data\Model;

class UI extends \atk4\data\Persistence
{

    public $date_format = 'd/m/Y';

    public $time_format = 'h:i:S';

    public $datetime_format = 'D, d M Y H:i:s O';


    public function _typecastSaveField(\atk4\data\Field $f, $value)
    {
        // work only on copied value not real one !!!
        $v = is_object($value) ? clone $value : $value;

        switch ($f->type) {
        case 'boolean':
        case 'money':
            return number_format($v, 2);
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


            // Expression and null cannot be converted.
            if (
                $value instanceof \atk4\dsql\Expression ||
                $value instanceof \atk4\dsql\Expressionable ||
                $value === null
            ) {
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
