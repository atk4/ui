<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Grid;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsFunction;
use Atk4\Ui\JsToast;
use Atk4\Ui\Table;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Country($app->db);
$grid = Grid::addTo($app, ['name' => 'grid']);
$grid->setModel($model);

$grid->addDecorator($model->fieldName()->name, [Table\Column\Link::class, 'url' => 'xxx']);

$grid->table->onRowClick(function () {
    return new JsToast(['message' => 'Clicked on row']);
});

$grid->addSelection();

// emulate navigate for <a> for Behat
// TODO emulate for all tests automatically in our Atk4\Ui\Behat\Context
$grid->table->js(true)->find('a')->on(
    'click',
    new JsFunction(['event'], [new JsExpression('window.location.href = \'#test\'; event.preventDefault();')])
);
