<?php

date_default_timezone_set('UTC');
include_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$app->add(['Header', 'Table column may contains popup or dropdown menu.']);

// Better Popup positionning when Popup are inside a container.
$container = $app->add(['ui' => 'vertical segment']);
$table = $container->add(['Table', 'celled' => true]);
$table->setModel(new SomeData(), false);

//will add popup to this column.
$col_name = $table->addColumn('name');

//will add dropdown menu to this colum.
$col_surname = $table->addColumn('surname');

$col_title = $table->addColumn('title');

$table->addColumn('date');
$table->addColumn('salary', new \atk4\ui\TableColumn\Money());

//regular popup setup
$col_name->addPopup()->add('Text')->set('Name popup');

//dynamic popup setup
//This popup will add content using the callback function.
$col_surname->addPopup()->set(function ($pop) {
    $pop->add('Text')->set('This popup is loaded dynamically');
});

//Another dropdown menu.
$col_title->addDropdown(['Change', 'Reorder', 'Update'], function ($item) {
    return 'Title item: ' . $item;
});

////////////////////////////////////////////////

$app->add(['Header', 'Grid column may contains popup or dropdown menu.']);

//Table in Grid are already inside a container.
$g = $app->add(['Grid']);
$g->setModel(new Country($db));
$g->ipp = 5;

//Adding a dropdown menu to the column 'name'.
$g->addDropdown('name', ['Rename', 'Delete'], function ($item) {
    return $item;
});

//Adding a popup view to the column 'iso'
$pop = $g->addPopup('iso');
$pop->add('Text')->set('Grid column popup');
