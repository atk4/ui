<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence;

use Atk4\Data\Model;

/**
 * Implements a very basic array-access pattern:.
 *
 * $m = new Model(Persistence\Static_(['hello', 'world']));
 * $m->load(1);
 *
 * echo $m->get('name'); // world
 */
class Static_ extends Array_
{
    /**
     * This will be the title field for the model.
     *
     * @var string
     */
    public $titleForModel;

    /**
     * Populate the following fields for the model.
     *
     * @var array<string, array>
     */
    public $fieldsForModel = [];

    /**
     * Constructor. Can pass array of data in parameters.
     *
     * @param array $data Static data in one of supported formats
     */
    public function __construct(array $data = null)
    {
        // chomp off first row, we will use it to deduct fields
        $row1 = reset($data);

        $this->onHookShort(self::HOOK_AFTER_ADD, function (...$args) {
            $this->afterAdd(...$args);
        });

        if (!is_array($row1)) {
            // convert array of strings into array of hashes
            foreach ($data as $k => $str) {
                $data[$k] = ['name' => $str];
            }
            unset($str);

            $this->titleForModel = 'name';
            $this->fieldsForModel = ['name' => []];

            parent::__construct($data);

            return;
        }

        if (isset($row1['name'])) {
            $this->titleForModel = 'name';
        } elseif (isset($row1['title'])) {
            $this->titleForModel = 'title';
        }

        $key_override = [];
        $def_types = [];
        $must_override = false;

        foreach ($row1 as $key => $value) {
            // id information present, use it instead
            if ($key === 'id') {
                $must_override = true;
            }

            // try to detect type of field by its value
            if (is_int($value)) {
                $def_types[] = ['type' => 'integer'];
            } elseif ($value instanceof \DateTime) {
                $def_types[] = ['type' => 'datetime'];
            } elseif (is_bool($value)) {
                $def_types[] = ['type' => 'boolean'];
            } elseif (is_float($value)) {
                $def_types[] = ['type' => 'float'];
            } elseif (is_array($value)) {
                $def_types[] = ['type' => 'json'];
            } elseif (is_object($value)) {
                $def_types[] = ['type' => 'object'];
            } else {
                $def_types[] = [];
            }

            // if title is not set, use first key
            if (!$this->titleForModel) {
                if (is_int($key)) {
                    $key_override[] = 'name';
                    $this->titleForModel = 'name';
                    $must_override = true;

                    continue;
                }

                $this->titleForModel = $key;
            }

            if (is_int($key)) {
                $key_override[] = 'field' . $key;
                $must_override = true;

                continue;
            }

            $key_override[] = $key;
        }

        if ($must_override) {
            $data2 = [];

            foreach ($data as $key => $row) {
                $row = array_combine($key_override, $row);
                if (isset($row['id'])) {
                    $key = $row['id'];
                }
                $data2[$key] = $row;
            }
            $data = $data2;
        }

        $this->fieldsForModel = array_combine($key_override, $def_types);
        parent::__construct($data);
    }

    /**
     * Automatically adds missing model fields.
     *
     * Called by HOOK_AFTER_ADD hook.
     */
    public function afterAdd(Model $model): void
    {
        if ($this->titleForModel) {
            $model->title_field = $this->titleForModel;
        }

        foreach ($this->fieldsForModel as $field => $def) {
            if ($model->hasField($field)) {
                continue;
            }

            // add new field
            $model->addField($field, $def);
        }
    }
}
