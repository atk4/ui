<?php

declare(strict_types=1);

namespace atk4\ui\Persistence;

use atk4\data\Model;
use atk4\ui\Exception;
use atk4\ui\Persistence\Type\Boolean;
use atk4\ui\Persistence\Type\Castable;
use atk4\ui\Persistence\Type\Date;
use atk4\ui\Persistence\Type\Money;
use atk4\ui\Persistence\Type\Serial;

/**
 * This class is used for typecasting model types to the values that will be presented to the user. App will
 * always initialize this persistence in $app->ui_persistence and this object will be used by various
 * UI elements to output data to the user.
 *
 * Value casting is perform via a Castable class associate to a field type using $typeClass property.
 * $typeClass property provide default class name for certain field type.
 * Changing value casting for each field type, or adding new one, can be done by registering new class.
 */
class Ui extends \atk4\data\Persistence
{
    public $typeClass = [
        'boolean' => Boolean::class,
        'date' => Date::class,
        'time' => Date::class,
        'datetime' => Date::class,
        'money' => Money::class,
        'array' => Serial::class,
        'object' => Serial::class,
    ];

    /**
     * Get Castable object instance associate to a field type.
     */
    public function getType(string $type): Castable
    {
        if (!isset($this->typeClass[$type])) {
            throw (new Exception('There is no class register with this field type.'))
                ->addMoreInfo('type', $type);
        }

        return new $this->typeClass[$type]();
    }

    /**
     * Associate a Castable class to a field type.
     */
    public function registerTypeClass(string $type, string $class)
    {
        $this->typeClass[$type] = $class;
    }

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

        // delegate value casting to proper class if set.
        if ($f->type && isset($this->typeClass[$f->type])) {
            $value = $this->getType($f->type)->castSaveValue($f, $value);
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
                throw (new Exception('Unable to serialize field value on load'))
                    ->addMoreInfo('serializator', $f->serialize)
                    ->addMoreInfo('value', $value)
                    ->addMoreInfo('field', $f);
            }
            $value = $new_value;
        }

        // always normalize string EOL
        if (is_string($value) && !$f->serialize) {
            $value = preg_replace('~\r?\n|\r~', "\n", $value);
        }

        // delegate value casting to proper class if set.
        if ($f->type && isset($this->typeClass[$f->type])) {
            $value = $this->getType($f->type)->castLoadValue($f, $value);
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
    public function typecastSaveRow(Model $model, array $row): array
    {
        $result = [];
        foreach ($row as $key => $value) {
            // Figure out the name of the destination field
            $field = $key;

            // We have no knowledge of the field, it wasn't defined, so
            // we will leave it as-is.
            if (!$model->hasField($key)) {
                $result[$field] = $value;

                continue;
            }

            $value = $this->typecastSaveField($model->getField($key), $value);

            // store converted value
            $result[$field] = $value;
        }

        return $result;
    }
}
