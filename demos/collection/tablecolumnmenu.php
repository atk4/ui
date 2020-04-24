<?php

chdir('..');
require_once 'init.php';

\atk4\ui\Header::addTo($app, ['Table column may contains popup or dropdown menu.']);

// Better Popup positionning when Popup are inside a container.
$container = \atk4\ui\View::addTo($app, ['ui' => 'vertical segment']);
$table = \atk4\ui\Table::addTo($container, ['celled' => true]);
$table->setModel(new SomeData(), false);

//will add popup to this column.
$col_name = $table->addColumn('name');

//will add dropdown menu to this colum.
$col_surname = $table->addColumn('surname');

$col_title = $table->addColumn('title');

$table->addColumn('date');
$table->addColumn('salary', new \atk4\ui\TableColumn\Money());

//regular popup setup
\atk4\ui\Text::addTo($col_name->addPopup())->set('Name popup');

//dynamic popup setup
//This popup will add content using the callback function.
$col_surname->addPopup()->set(function ($pop) {
    \atk4\ui\Text::addTo($pop)->set('This popup is loaded dynamically');
});

//Another dropdown menu.
$col_title->addDropdown(['Change', 'Reorder', 'Update'], function ($item) {
    return 'Title item: ' . $item;
});

////////////////////////////////////////////////

\atk4\ui\Header::addTo($app, ['Grid column may contains popup or dropdown menu.']);

//Table in Grid are already inside a container.
$g = \atk4\ui\Grid::addTo($app);
$g->setModel(new Country($db));
$g->ipp = 5;

//Adding a dropdown menu to the column 'name'.
$g->addDropdown('name', ['Rename', 'Delete'], function ($item) {
    return $item;
});

//Adding a popup view to the column 'iso'
$pop = $g->addPopup('iso');
\atk4\ui\Text::addTo($pop)->set('Grid column popup');
