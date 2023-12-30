<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

class FakerPersistence extends Persistence
{
    /** @var FakerGenerator */
    public $faker;

    /** @var int */
    public $count = 5;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    public function prepareIterator(Model $model): \Traversable
    {
        foreach ($this->export($model) as $row) {
            yield $row;
        }
    }

    private function export(Model $model, array $fields = []): array
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

                if ($field === $model->idField) {
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

        return $data;
    }
}
