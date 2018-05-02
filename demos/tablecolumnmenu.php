<?php

date_default_timezone_set('UTC');
include 'init.php';
require 'database.php';

$app->add(['Header', 'Table column may contains popup or dropdown menu.']);

    $table = $app->add(['ui'=>'segment'])->add(['Table', 'celled' => true]);
    $table->setModel(new Country($app->db));

    $name_column = $table->getColumnDecorators('name');
    $name_column[0]->addPopup()->set(function ($p) {
        $p->add('HelloWorld');
    });

exit;

//For popup positioning to work correctly, table need to be inside a view segment.
$view = $app->add('View', ['ui' => 'basic segment']);

$table = $view->add(['Table', 'celled' => true]);
$table->setModel(new SomeData(), false);

//will add popup to this column.
$col_name = $table->addColumn('name');

//will add dropdown menu to this colum.
$col_surname = $table->addColumn('surname');

$col_title = $table->addColumn('title');

$table->addColumn('date');
$table->addColumn('salary', new \atk4\ui\TableColumn\Money());

//regular popup setup
$col_name->addPopup()->add('View')->set('Name popup');

//dynamic popup setup
//This popup will add content using the callback function.
$col_surname->addPopup()->set(function ($pop) {
    $pop->add('View')->set('This popup is loaded dynamically');
});

//Another dropdown menu.
$col_title->addDropdown(['Change', 'Reorder', 'Update'], function ($item) {
    return 'Title item: '.$item;
});

////////////////////////////////////////////////

$app->add(['Header', 'Grid column may contains popup or dropdown menu.']);

//For popup positioning to work correctly, grid need to be inside a view segment.
$view = $app->add('View', ['ui' => 'basic segment']);
$g = $view->add(['Grid']);
$g->setModel(new Country($db));
$g->ipp = 5;

//Adding a dropdown menu to the column 'name'.
$g->addDropdown('name', ['Rename', 'Delete'], function ($item) {
    return $item;
});

//Adding a popup view to the column 'iso'
$pop = $g->addPopup('iso');
$pop->add('View')->set('Grid column popup');
