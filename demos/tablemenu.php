<?php

date_default_timezone_set('UTC');
include 'init.php';

$table = $app->add(['Table', 'celled' => true]);

//$table->addTableMenu([['name'=> 'Customize Field', 'value' => 'customize']]);
$table->columMenus = [['name'=> 'Customize Field', 'value' => 'customize'], ['name' => 'Rename', 'value' => 'rename']];

$table->setModel(new SomeData(), false);

$table->addColumn('name', new \atk4\ui\TableColumn\Link(['details', 'id' => '{$id}']));
$table->addColumn('surname', new \atk4\ui\TableColumn\Template('{$surname}'))->addClass('warning');
$table->addColumn('title');

$table->addColumn('date');
$table->addColumn('salary', new \atk4\ui\TableColumn\Money());

$table->js(true, (new atk4\ui\jQuery('.atk-table-dropdown .dropdown'))
    ->dropdown([
                'action'  => 'hide',
                'values'  => $table->columMenus,
               ]));
