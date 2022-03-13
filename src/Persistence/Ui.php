<?php

declare(strict_types=1);

namespace Atk4\Ui\Persistence;

use Atk4\Data\Field;
use Atk4\Data\Field\PasswordField;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Exception;

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
class Ui extends Persistence
{
    /** @var string */
    public $locale = 'en';

    /** @var string */
    public $currency = '€';
    /** @var int Default decimal count for 'atk4_money' type. */
    public $currency_decimals = 2;

    /** @var string */
    public $timezone;
    /** @var string */
    public $date_format = 'M d, Y';
    /** @var string */
    public $time_format = 'H:i';
    /** @var string */
    public $datetime_format = 'M d, Y H:i:s';
    /** @var int Calendar input first day of week. 0 = sunday. */
    public $firstDayOfWeek = 0;

    /** @var string */
    public $yes = 'Yes';
    /** @var string */
    public $no = 'No';

    public function __construct()
    {
        if ($this->timezone === null) {
            $this->timezone = date_default_timezone_get();
        }
    }

    public function typecastSaveField(Field $field, $value)
    {
        // relax empty checks for UI render for not yet set values
        $fieldRequiredOrig = $field->required;
        $fieldMandatoryOrig = $field->mandatory;
        if (in_array($value, [null, false, 0, 0.0, ''], true)) {
            $field->required = false;
            $field->mandatory = false;
        }
        try {
            return parent::typecastSaveField($field, $value);
        } finally {
            $field->required = $fieldRequiredOrig;
            $field->mandatory = $fieldMandatoryOrig;
        }
    }

    /**
     * This method contains the logic of casting generic values into user-friendly format.
     */
    protected function _typecastSaveField(Field $field, $value): string
    {
        // always normalize string EOL
        if (is_string($value)) {
            $value = preg_replace('~\r?\n|\r~', "\n", $value);
        }

        // typecast using DBAL types
        $value = parent::_typecastSaveField($field, $value);

        switch ($field->type) {
            case 'boolean':
                $value = parent::_typecastLoadField($field, $value);
                $value = $value ? $this->yes : $this->no;

                break;
            case 'atk4_money':
                $value = parent::_typecastLoadField($field, $value);
                $value = ($this->currency ? $this->currency . ' ' : '') . number_format($value, $this->currency_decimals);

                break;
            case 'date':
            case 'datetime':
            case 'time':
                $value = parent::_typecastLoadField($field, $value);
                if ($value instanceof \DateTimeInterface) {
                    $formats = ['date' => $this->date_format, 'datetime' => $this->datetime_format, 'time' => $this->time_format];
                    $format = $field->persist_format ?: $formats[$field->type];

                    if ($field->type === 'datetime') {
                        $value = new \DateTime($value->format('Y-m-d H:i:s.u'), $value->getTimezone());
                        $value->setTimezone(new \DateTimeZone($this->timezone));
                    }
                    $value = $value->format($format);
                }

                break;
        }

        return (string) $value;
    }

    /**
     * Interpret user-defined input for various types.
     */
    protected function _typecastLoadField(Field $field, $value)
    {
        // always normalize string EOL
        if (is_string($value)) {
            $value = preg_replace('~\r?\n|\r~', "\n", $value);
        }

        switch ($field->type) {
            case 'date':
            case 'datetime':
            case 'time':
                if ($value === '') {
                    return null;
                }

                $dtClass = \DateTime::class;
                $tzClass = \DateTimeZone::class;

                // ! symbol in date format is essential here to remove time part of DateTime - don't remove, this is not a bug
                $formats = ['date' => '!+' . $this->date_format, 'datetime' => '!+' . $this->datetime_format, 'time' => '!+' . $this->time_format];
                $format = $field->persist_format ?: $formats[$field->type];

                $valueStr = is_object($value) ? $this->_typecastSaveField($field, $value) : $value;
                $isDatetime = $field->type === 'datetime';
                $value = $dtClass::createFromFormat($format, $valueStr, $isDatetime ? new $tzClass($this->timezone) : null);
                if ($value === false) {
                    throw (new Exception('Incorrectly formatted datetime'))
                        ->addMoreInfo('format', $format)
                        ->addMoreInfo('value', $valueStr)
                        ->addMoreInfo('field', $field);
                }
                if ($isDatetime) {
                    $value->setTimezone(new $tzClass(date_default_timezone_get()));
                }

                $value = parent::_typecastSaveField($field, $value);

                break;
            // SECURTIY: Do not unserialize any user input
            // https://github.com/search?q=unserialize+repo%3Adoctrine%2Fdbal+path%3A%2Fsrc%2FTypes
            case 'object':
            case 'array':
                throw new Exception('Object serialization is not supported');
        }

        if ($field->getReference() !== null) {
            if (empty($value)) {
                return null;
            }
        }

        // typecast using DBAL types
        $value = parent::_typecastLoadField($field, $value);

        if ($value !== null && $field instanceof PasswordField && !$field->hashPasswordIsHashed($value)) {
            $value = $field->hashPassword($value);
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
    public function typecastSaveRow(Model $model, array $row): array
    {
        $result = [];
        foreach ($row as $key => $value) {
            // no knowledge of the field, it wasn't defined, leave it as-is
            if (!$model->hasField($key)) {
                $result[$key] = $value;

                continue;
            }

            $result[$key] = $this->typecastSaveField($model->getField($key), $value);
        }

        return $result;
    }
}
