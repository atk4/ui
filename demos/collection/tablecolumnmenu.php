<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Grid;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Table;
use Atk4\Ui\Text;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Table column may contains popup or dropdown menu.']);

// better Popup positioning when Popup are inside a container
$container = View::addTo($app, ['ui' => 'vertical segment']);
$table = Table::addTo($container, ['class.celled' => true]);
$table->setModel(new SomeData(), []);

// will add popup to this column
$colName = $table->addColumn('name');

// will add dropdown menu to this column
$colSurname = $table->addColumn('surname');

$colTitle = $table->addColumn('title');

$table->addColumn('date');
$table->addColumn('salary', new Table\Column\Money());

// regular popup setup
Text::addTo($colName->addPopup())->set('Name popup');

// dynamic popup setup
// this popup will add content using the callback function
$colSurname->addPopup()->set(static function (View $pop) {
    Text::addTo($pop)->set('This popup is loaded dynamically');
});

// another dropdown menu
$colTitle->addDropdown(['Change', 'Reorder', 'Update'], static function (string $item) {
    return new JsToast(['message' => 'Title item: ' . $item]);
});

// -----------------------------------------------------------------------------

Header::addTo($app, ['Grid column may contains popup or dropdown menu.']);

// Table in Grid are already inside a container
$grid = Grid::addTo($app);
$grid->setModel(new Country($app->db));
$grid->ipp = 5;

// adding a dropdown menu to the column 'name'
$grid->addDropdown(Country::hinting()->fieldName()->name, ['Rename', 'Delete'], static function (string $item) {
    return $item;
});

// adding a popup view to the column 'iso'
$pop = $grid->addPopup(Country::hinting()->fieldName()->iso);
Text::addTo($pop)->set('Grid column popup');
