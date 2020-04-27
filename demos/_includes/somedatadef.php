<?php

if (!class_exists('SomeData')) {
    class SomeData extends \atk4\data\Model
    {
        public function __construct()
        {
            $p = new Persistence_Faker();

            parent::__construct($p);
        }

        public function init(): void
        {
            parent::init();
            $m = $this;

            $m->addField('title');
            $m->addField('name');
            $m->addField('surname', ['actual' => 'name']);
            $m->addField('date', ['type' => 'date']);
            $m->addField('salary', ['type' => 'money', 'actual' => 'randomNumber']);
            $m->addField('logo_url');
        }
    }

    class Persistence_Faker extends \atk4\data\Persistence
    {
        public $faker;

        public $count = 5;

        public function __construct($opts = [])
        {
            //parent::__construct($opts);

            if (!$this->faker) {
                $this->faker = Faker\Factory::create();
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

                    if ($field == $m->id_field) {
                        $row[$field] = $i + 1;

                        continue;
                    }

                    $actual = $m->getField($field)->actual;
                    if ($actual) {
                        $type = $actual;
                    }

                    if ($type == 'logo_url') {
                        $row[$field] = '../images/' . ['default.png', 'logo.png'][rand(0, 1)]; // one of these
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
}
