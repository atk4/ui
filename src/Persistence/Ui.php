<?php

declare(strict_types=1);

namespace atk4\ui\Persistence;

use atk4\data\Model;
use atk4\ui\Exception;
use atk4\ui\Persistence\Type\Boolean;
use atk4\ui\Persistence\Type\Date;
use atk4\ui\Persistence\Type\Money;
use atk4\ui\Persistence\Type\Serial;

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
class Ui extends \atk4\data\Persistence
{
    public $boolean = Boolean::class;
    public $date = Date::class;
    public $time = Date::class;
    public $datetime = Date::class;
    public $money = Money::class;
    public $array = Serial::class;
    public $object = Serial::class;

    public function getTypeClass(string $type): ?string
    {
        return $this->{$type} ?? null;
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
        if ($f->type && $this->getTypeClass($f->type)) {
            $value = $this->getTypeClass($f->type)::castSaveValue($f, $value);
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
        if ($f->type && $this->getTypeClass($f->type)) {
            $value = $this->getTypeClass($f->type)::castLoadValue($f, $value);
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
