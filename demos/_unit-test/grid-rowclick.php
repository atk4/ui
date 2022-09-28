<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Grid;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsFunction;
use Atk4\Ui\JsToast;
use Atk4\Ui\Message;
use Atk4\Ui\Table;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Country($app->db);
$grid = Grid::addTo($app, ['name' => 'grid']);
$grid->setModel($model);

$grid->addDecorator($model->fieldName()->name, [Table\Column\Link::class, 'url' => 'xxx']);

$grid->addActionButton('Action Button', function () {
    return new JsToast(['message' => 'Clicked Action Button']);
});

$grid->addActionMenuItem('Action MenuItem', function () {
    return new JsToast(['message' => 'Clicked Action MenuItem']);
});

$grid->addModalAction('Action Modal', 'Details', function (View $p, $id) use ($model) {
    Message::addTo($p, ['Clicked Action Modal: ' . $model->load($id)->name]);
});

$grid->table->onRowClick(function () {
    return new JsToast(['message' => 'Clicked on row']);
});

$grid->addSelection();

// emulate navigate for <a> for Behat
// TODO emulate for all tests automatically in our Atk4\Ui\Behat\Context
// this emulation is not perfect, as it works even with event.preventDefault() called
$grid->table->js(true)->find('a')->on(
    'click',
    new JsFunction(['event'], [new JsExpression('window.location.href = \'#test\'; event.preventDefault();')])
);
