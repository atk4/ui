<?php

declare(strict_types=1);

namespace atk4\ui\Persistence\Type;

use atk4\data\Field;
use atk4\ui\Exception;

class Date extends Basic implements Castable
{
    /** @var string[] Format use by form controls. */
    public static $props = [
        'date' => 'M d, Y',
        'time' => 'H:i:s',
        'datetime' => 'Y-m-d H:i:s',
    ];

    public static function castLoadValue(Field $field, $value)
    {
        $dt_class = $field->dateTimeClass ?? \DateTime::class;
        $tz_class = $field->dateTimeZoneClass ?? \DateTimeZone::class;

        // ! symbol in date format is essential here to remove time part of DateTime - don't remove, this is not a bug
        $format = $field->persist_format ?: '!+' . static::getProps($field->type);

        // datetime only - set from persisting timezone
        $valueStr = $value;
        if ($field->type === 'datetime' && isset($field->persist_timezone)) {
            $value = $dt_class::createFromFormat($format, $value, new $tz_class($field->persist_timezone));
            if ($value === false) {
                throw (new Exception('Incorrectly formatted datetime'))
                    ->addMoreInfo('format', $format)
                    ->addMoreInfo('value', $valueStr)
                    ->addMoreInfo('field', $field);
            }
            $value->setTimeZone(new $tz_class(date_default_timezone_get()));
        } else {
            $value = $dt_class::createFromFormat($format, $value);
            if ($value === false) {
                throw (new Exception('Incorrectly formatted date/time'))
                    ->addMoreInfo('format', $format)
                    ->addMoreInfo('value', $valueStr)
                    ->addMoreInfo('field', $field);
            }
        }

        return $value;
    }

    public static function castSaveValue(Field $field, $value)
    {
        $dt_class = $f->dateTimeClass ?? \DateTime::class;
        $tz_class = $f->dateTimeZoneClass ?? \DateTimeZone::class;

        if ($value instanceof $dt_class || $value instanceof \DateTimeInterface) {
            $format = $field->persist_format ?: static::getProps($field->type);

            // datetime only - set to persisting timezone
            if ($field->type === 'datetime' && isset($field->persist_timezone)) {
                $value = new $dt_class($value->format('Y-m-d H:i:s.u'), $value->getTimezone());
                $value->setTimezone(new $tz_class($field->persist_timezone));
            }
            $value = $value->format($format);
        }

        return $value;
    }
}
