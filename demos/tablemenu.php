<?php

date_default_timezone_set('UTC');
include 'init.php';

$table = $app->add(['Table', 'celled' => true]);
$table->setModel(new SomeData(), false);

//will add popup to this column.
$col_name = $table->addColumn('name', new \atk4\ui\TableColumn\Link(['details', 'id' => '{$id}']));

//will add dropdown menu to this colum.
$col_surname = $table->addColumn('surname', new \atk4\ui\TableColumn\Template('{$surname}'))->addClass('warning');

$col_title = $table->addColumn('title');
$table->addColumn('date');
$table->addColumn('salary', new \atk4\ui\TableColumn\Money());

//popup setup
$col_name->addPopup('name')->add('View')->set('Testing popup');

//dropdown menu
$col_surname->addDropdown('surname', ['Customize', 'Rename', 'Update'], function ($item) {
    return 'Surname item: '.$item;
});

$col_title->addDropdown('title', ['Change', 'Reorder', 'Update'], function ($item) {
    return 'Title item: '.$item;
});
