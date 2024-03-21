<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\Columns;
use Atk4\Ui\Form;
use Atk4\Ui\Grid;
use Atk4\Ui\Loader;
use Atk4\Ui\Text;
use Atk4\Ui\View;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Loader Example - page 1', 'class.small left floated basic blue' => true, 'icon' => 'left arrow'])
    ->link(['loader']);
View::addTo($app, ['ui' => 'clearing divider']);

$c = Columns::addTo($app);

$grid = Grid::addTo($c->addColumn(), ['ipp' => 10, 'menu' => false]);
$grid->setModel(new Country($app->db), [Country::hinting()->fieldName()->name]);

$countryLoader = Loader::addTo($c->addColumn(), ['loadEvent' => false, 'shim' => [Text::class, 'Select country on your left']]);

$grid->table->onRowClick($countryLoader->jsLoad(['id' => $grid->jsRow()->data('id')]));

$countryLoader->set(static function (Loader $p) {
    $country = new Country($p->getApp()->db);
    $id = $p->getApp()->uiPersistence->typecastAttributeLoadField($country->getIdField(), $p->getApp()->getRequestQueryParam('id'));
    Form::addTo($p)->setModel($country->load($id));
});
