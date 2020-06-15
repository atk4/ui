<?php

declare(strict_types=1);

namespace atk4\ui\demo;

class Persistence_Faker extends \atk4\data\Persistence
{
    public $faker;

    public $count = 5;

    public function __construct($opts = [])
    {
        //parent::__construct($opts);

        if (!$this->faker) {
            $this->faker = \Faker\Factory::create();
        }
    }

    public function prepareIterator($m)
    {
        foreach ($this->export($m) as $row) {
            yield $row;
        }
    }

    public function export($m, $fields = [])
    {
        if (!$fields) {
            foreach ($m->getFields() as $name => $e) {
                $fields[] = $name;
            }
        }

        $data = [];
        for ($i = 0; $i < $this->count; ++$i) {
            $row = [];
            foreach ($fields as $field) {
                $type = $field;

                if ($field === $m->id_field) {
                    $row[$field] = $i + 1;

                    continue;
                }

                $actual = $m->getField($field)->actual;
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

        return array_map(function ($r) use ($m) {
            return $this->typecastLoadRow($m, $r);
        }, $data);
    }
}
