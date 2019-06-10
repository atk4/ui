<?php

require 'init.php';
require 'database.php';

class Plans extends \atk4\data\Model
{
    public function init()
    {
        parent::init();
        $this->addField('name');
        $this->addField('apps', ['type' => 'integer', 'caption' => 'Apps']);
        $this->addField('space', ['caption' => 'Gb space']);

        $this->addAction('Sign Up', function ($m) {
            $len = strlen(file_get_contents($m['name']));

            return "$len bytes downloaded..";
        });
    }
}
$data = [
    ['id' => 1, 'name' => 'Hobbyist', 'apps' => 1, 'space' => 10],
    ['id' => 2, 'name' => 'Developer', 'apps' => 3, 'space' => 100],
    ['id' => 3, 'name' => 'Enterprise', 'apps' => 3, 'space' => 100],
];
$plan = new Plans(new \atk4\data\Persistence\Array_($data));


$deck = $app->add(['ui' => 'cards']);

$plan->each(function($m) use ($deck) {
   $c = $deck->add(['CardHolder', 'useLabel' => true]);
   $c->setModel($m, ['name', 'apps', 'space']);
});