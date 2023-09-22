<?php

declare(strict_types=1);

namespace Atk4\Ui\Persistence;

use Atk4\Data\Field;
use Atk4\Data\Field\PasswordField;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Data\Persistence\Sql\Expression;
use Atk4\Ui\Exception;

/**
 * This class is used for typecasting model types to the values that will be presented to the user. App will
 * always initialize this persistence in $app->uiPersistence and this object will be used by various
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

    /** @var '.'|',' Decimal point separator for numeric (non-integer) types. */
    public $decimalSeparator = '.';
    /** @var ''|' '|','|'.' Thousands separator for numeric types. */
    public $thousandsSeparator = ' ';

    /** @var string Currency symbol for 'atk4_money' type. */
    public $currency = '€';
    /** @var int Number of decimal digits for 'atk4_money' type. */
    public $currencyDecimals = 2;

    /** @var string */
    public $timezone;
    /** @var string */
    public $dateFormat = 'M j, Y';
    /** @var string */
    public $timeFormat = 'H:i';
    /** @var string */
    public $datetimeFormat = 'M j, Y H:i';
    /** @var int Calendar input first day of week, 0 = Sunday, 1 = Monday. */
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

    /**
     * @return scalar|null
     */
    public function typecastSaveField(Field $field, $value)
    {
        // relax empty checks for UI render for not yet set values
        $fieldNullableOrig = $field->nullable;
        $fieldRequiredOrig = $field->required;
        if (in_array($value, [null, false, 0, 0.0, ''], true)) {
            $field->nullable = true;
            $field->required = false;
        }
        try {
            return parent::typecastSaveField($field, $value);
        } finally {
            $field->nullable = $fieldNullableOrig;
            $field->required = $fieldRequiredOrig;
        }
    }

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
            case 'integer':
            case 'float':
                $value = parent::_typecastLoadField($field, $value);
                $value = is_int($value)
                    ? (string) $value
                    : Expression::castFloatToString($value);
                $value = preg_replace_callback('~\.?\d+~', function ($matches) {
                    return substr($matches[0], 0, 1) === '.'
                        ? $this->decimalSeparator . preg_replace('~\d{3}\K(?!$)~', '', substr($matches[0], 1))
                        : preg_replace('~(?<!^)(?=(?:\d{3})+$)~', $this->thousandsSeparator, $matches[0]);
                }, $value);
                $value = str_replace(' ', "\u{00a0}" /* Unicode NBSP */, $value);

                break;
            case 'atk4_money':
                $value = parent::_typecastLoadField($field, $value);
                $valueDecimals = strlen(preg_replace('~^[^.]$|^.+\.|0+$~s', '', number_format($value, max(0, 11 - (int) log10($value)), '.', '')));
                $value = ($this->currency ? $this->currency . ' ' : '')
                    . number_format($value, max($this->currencyDecimals, $valueDecimals), $this->decimalSeparator, $this->thousandsSeparator);
                $value = str_replace(' ', "\u{00a0}" /* Unicode NBSP */, $value);

                break;
            case 'date':
            case 'datetime':
            case 'time':
                /** @var \DateTimeInterface|null */
                $value = parent::_typecastLoadField($field, $value);
                if ($value !== null) {
                    $format = [
                        'date' => $this->dateFormat,
                        'datetime' => $this->datetimeFormat,
                        'time' => $this->timeFormat,
                    ][$field->type];

                    $valueHasSeconds = (int) $value->format('s') !== 0;
                    $valueHasMicroseconds = (int) $value->format('u') !== 0;
                    $formatHasMicroseconds = str_contains($format, '.u');
                    if ($valueHasSeconds || $valueHasMicroseconds) {
                        $format = preg_replace('~(?<=:i)(?!:s)~', ':s', $format);
                    }
                    if ($valueHasMicroseconds) {
                        $format = preg_replace('~(?<=:s)(?!\.u)~', '.u', $format);
                    }

                    if ($field->type === 'datetime') {
                        $value = new \DateTime($value->format('Y-m-d H:i:s.u'), $value->getTimezone());
                        $value->setTimezone(new \DateTimeZone($this->timezone));
                    }
                    $value = $value->format($format);

                    if (!$formatHasMicroseconds) {
                        $value = preg_replace('~(?<!\d|:)\d{1,2}:\d{1,2}(?::\d{1,2})?\.\d*?\K0+(?!\d)~', '', $value);
                    }
                }

                break;
        }

        return (string) $value;
    }

    protected function _typecastLoadField(Field $field, $value)
    {
        switch ($field->type) {
            case 'boolean':
                if (is_string($value)) {
                    $value = trim($value);
                    if (mb_strtolower($value) === mb_strtolower($this->yes)) {
                        $value = '1';
                    } elseif (mb_strtolower($value) === mb_strtolower($this->no)) {
                        $value = '0';
                    }
                }

                break;
            case 'integer':
            case 'float':
            case 'atk4_money':
                if (is_string($value)) {
                    $dSep = $this->decimalSeparator;
                    $tSep = $this->thousandsSeparator;
                    if ($tSep !== '.' && $tSep !== ',' && !str_contains($value, $dSep)) {
                        if (str_contains($value, '.')) {
                            $dSep = '.';
                        } elseif (str_contains($value, ',')) {
                            $dSep = ',';
                        }
                    }

                    $value = str_replace([' ', "\u{00a0}" /* Unicode NBSP */, '_', $tSep], '', $value);
                    $value = str_replace($dSep, '.', $value);

                    if ($field->type === 'atk4_money' && $this->currency !== '' && substr_count($value, $this->currency) === 1) {
                        $currencyPos = strpos($value, $this->currency);
                        $beforeStr = substr($value, 0, $currencyPos);
                        $afterStr = substr($value, $currencyPos + strlen($this->currency));

                        $value = $beforeStr
                            . (ctype_digit(substr($beforeStr, -1)) && ctype_digit(substr($afterStr, 0, 1)) ? '.' : '')
                            . $afterStr;
                    }
                }

                break;
            case 'date':
            case 'datetime':
            case 'time':
                if ($value === '') {
                    return null;
                }

                $dtClass = \DateTime::class;
                $tzClass = \DateTimeZone::class;
                $format = [
                    'date' => $this->dateFormat,
                    'datetime' => $this->datetimeFormat,
                    'time' => $this->timeFormat,
                ][$field->type];

                if (preg_match('~(?<!\d|:)\d{1,2}:\d{1,2}:\d{1,2}(?!\d)~', $value)) {
                    $format = preg_replace('~(?<=:i)(?!:s)~', ':s', $format);
                }
                if (preg_match('~(?<!\d|:)\d{1,2}:\d{1,2}(?::\d{1,2})?\.\d{1,9}(?!\d)~', $value)) {
                    $format = preg_replace('~(?<=:s)(?!\.u)~', '.u', $format);
                }

                $valueOrig = $value;
                $value = $dtClass::createFromFormat('!' . $format, $value, $field->type === 'datetime' ? new $tzClass($this->timezone) : null);
                if ($value === false) {
                    throw (new Exception('Incorrectly formatted datetime'))
                        ->addMoreInfo('format', $format)
                        ->addMoreInfo('value', $valueOrig)
                        ->addMoreInfo('field', $field);
                }

                if ($field->type === 'datetime') {
                    $value->setTimezone(new $tzClass(date_default_timezone_get()));
                }

                $value = parent::_typecastSaveField($field, $value);

                break;
                // <-- reindent once https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/6490 is merged
                // SECURITY: do not unserialize any user input
                // https://github.com/search?q=unserialize+repo%3Adoctrine%2Fdbal+path%3A%2Fsrc%2FTypes
            case 'object':
            case 'array':
                throw new Exception('Object serialization is not supported');
        }

        // typecast using DBAL type and normalize
        $value = parent::_typecastLoadField($field, $value);
        $value = (new Field(['type' => $field->type]))->normalize($value);

        if ($field->hasReference() && $value === '') {
            return null;
        }

        if ($value !== null && $field instanceof PasswordField && !$field->hashPasswordIsHashed($value)) {
            $value = $field->hashPassword($value);
        }

        return $value;
    }

    /**
     * Override parent method to ignore key change by Field::actual property.
     */
    public function typecastSaveRow(Model $model, array $row): array
    {
        $result = [];
        foreach ($row as $key => $value) {
            $result[$key] = $this->typecastSaveField($model->getField($key), $value);
        }

        return $result;
    }
}
