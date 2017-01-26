<?php
/**
 * Demonstrates how to use layouts.
 */
require '../vendor/autoload.php';

class Persistence_Faker extends \atk4\data\Persistence
{
    public $faker = null;

    public $count = 50;

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
            foreach ($m->elements as $name=>$e) {
                if ($e instanceof \atk4\data\Field) {
                    $fields[] = $name;
                }
            }
        }

        $data = [];
        for ($i = 0; $i < $this->count; $i++) {
            $row = [];
            foreach ($fields as $field) {
                $type = $field;

                if ($field == $m->id_field) {
                    $row[$field] = $i + 1;
                    continue;
                }

                $actual = $m->getElement($field)->actual;
                if ($actual) {
                    $type = $actual;
                }

                $row[$field] = $this->faker->$type;
            }
            $data[] = $row;
        }

        return array_map(function ($r) use ($m) {
            return $this->typecastLoadRow($m, $r);
        }, $data);
    }
}

try {
    $app = new \atk4\ui\App('Hello World');
    $db = new Persistence_Faker();

    $app->initLayout('Centered');

    $app->layout->add('HelloWorld');

    /*
    $m_comp = $app->menu->addMenu('Components');
    $m_comp->addItem(['Demo1', 'icon'=>'form'], ['demo'=>'form']);
    $m_comp->addItem(['Demo2', 'icon'=>'crud'], ['demo'=>'crud']);

    var_dump($_GET);
     */

    /*






    $m = new \atk4\data\Model($p);

    $m->addField('date', ['type'=>'date']);
    $m->addField('contact', ['actual'=>'name']);

    $layout->add(new \atk4\ui\Lister(), 'Report')
        ->setModel($m);
     */

    $app->run();
} catch (\atk4\core\Exception $e) {
    var_dump($e->getMessage());

    var_dump($e->getParams());
    throw $e;
}
