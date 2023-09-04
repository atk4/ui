<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Crud;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Category($app->db);

$crud = Crud::addTo($app);
$crud->setModel($model);

$crud->addModalAction(['icon' => 'book'], 'Edit product category', static function (View $v, $id) use ($model) {
    $entity = $model->load($id);

    $innerCrud = Crud::addTo($v);
    $innerCrud->setModel($entity->Products);
});
