<?php

date_default_timezone_set('UTC');
include 'init.php';

$table = $app->add(['Table', 'celled' => true]);
$table->setModel(new SomeData(), false);

//will add popup to this column.
$col_name = $table->addColumn('name', new \atk4\ui\TableColumn\Link(['details', 'id' => '{$id}']));

//will add dropdown menu to this colum.
$col_surname = $table->addColumn('surname', new \atk4\ui\TableColumn\Template('{$surname}'))->addClass('warning');

$table->addColumn('title');
$table->addColumn('date');
$table->addColumn('salary', new \atk4\ui\TableColumn\Money());

//popup setup
$pop = $app->add('Popup')->setHoverable();
$pop->add('View')->set('Testing popup.');
$col_name->addHeaderPopup($pop);

//dropdown setup.
$menu = $col_surname->addHeaderDropdown('surname', [['name'=> 'Customize', 'value' => 'customize'], ['name' => 'Rename', 'value' => 'rename']]);

$menu->onChangeItem(function ($menu, $item) {
    return new atk4\ui\jsNotify($menu.' / '.$item);
});

// testing
//$table->js(true, (new atk4\ui\jQuery('.atk-table-dropdown .dropdown'))
//    ->dropdown([
//                'action'  => 'hide',
//                'values'  => [['name'=> 'Customize Field', 'value' => 'customize'], ['name' => 'Rename', 'value' => 'rename']],
//               ]));
