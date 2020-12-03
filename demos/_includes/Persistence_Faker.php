<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

class Persistence_Faker extends \Atk4\Data\Persistence
{
    /** @var \Faker\Generator */
    public $faker;

    /** @var int */
    public $count = 5;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    public function prepareIterator($model)
    {
        foreach ($this->export($model) as $row) {
            yield $row;
        }
    }

    public function export($model, $fields = [])
    {
        if (!$fields) {
            foreach ($model->getFields() as $name => $e) {
                $fields[] = $name;
            }
        }

        $data = [];
        for ($i = 0; $i < $this->count; ++$i) {
            $row = [];
            foreach ($fields as $field) {
                $type = $field;

                if ($field === $model->id_field) {
                    $row[$field] = $i + 1;

                    continue;
                }

                $actual = $model->getField($field)->actual;
                if ($actual) {
                    $type = $actual;
                }

                if ($type === 'logo_url') {
                    $row[$field] = '../images/' . ['default.png', 'logo.png'][random_int(0, 1)]; // one of these
                } else {
                    $row[$field] = $this->faker->{$type};
                }
            }
            $data[] = $row;
        }

        return array_map(function ($row) use ($model) {
            return $this->typecastLoadRow($model, $row);
        }, $data);
    }
}
