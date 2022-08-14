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

    /** @var string Currency symbol for 'atk4_money' type. */
    public $currency = '€';
    /** @var int Number of decimal digits for 'atk4_money' type. */
    public $currencyDecimals = 2;
    /** @var string Decimal point separator for 'atk4_money' type. */
    public $currencyDecimalSeparator = '.';
    /** @var string Thousands separator for 'atk4_money' type. */
    public $currencyThousandsSeparator = ' ';

    /** @var string */
    public $timezone;
    /** @var string */
    public $dateFormat = 'M d, Y';
    /** @var string */
    public $timeFormat = 'H:i';
    /** @var string */
    public $datetimeFormat = 'M d, Y H:i:s';
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
                $valueDecimals = strlen(preg_replace('~^[^.]$|^.+\.|0+$~s', '', number_format($value, max(0, 11 - (int) log10($value)), '.', '')));
                $value = ($this->currency ? $this->currency . ' ' : '')
                    . number_format($value, max($this->currencyDecimals, $valueDecimals), $this->currencyDecimalSeparator, $this->currencyThousandsSeparator);
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
        switch ($field->type) {
            case 'boolean':
                if (is_string($value)) {
                    if (mb_strtolower($value) === mb_strtolower($this->yes)) {
                        $value = '1';
                    } elseif (mb_strtolower($value) === mb_strtolower($this->no)) {
                        $value = '0';
                    }
                }

                break;
            case 'atk4_money':
                if (is_string($value)) {
                    $value = str_replace([' ', "\u{00a0}" /* Unicode NBSP */, '_', $this->currency, '$', '€'], '', $value);
                    $dSep = $this->currencyDecimalSeparator;
                    $tSeps = array_filter(
                        array_unique([$dSep, $this->currencyThousandsSeparator, '.', ',']),
                        fn ($sep) => strpos($value, $sep) !== false
                    );
                    usort($tSeps, fn ($sepA, $sepB) => strrpos($value, $sepB) <=> strrpos($value, $sepA));
                    foreach ($tSeps as $tSep) {
                        if ($tSep === $dSep || strlen($value) - strrpos($value, $tSep) !== 4) {
                            $dSep = $tSep;

                            break;
                        }
                    }
                    $value = str_replace(array_diff($tSeps, [$dSep]), '', $value);
                    $value = str_replace($dSep, '.', $value);
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
                // SECURTIY: Do not unserialize any user input
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
