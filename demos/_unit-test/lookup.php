<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Crud;

// Test for hasOne Lookup as dropdown control.

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new ProductLock($app->db);
$model->addCondition('name', '=', 'Mustard');

$edit = $model->getUserAction('edit');
$edit->ui = ['execButton' => [\atk4\ui\Button::class, 'EditMe', 'blue']];
$edit->description = 'edit';
$edit->callback = function ($model) {
    return $model->ref('product_category_id')->getTitle() . ' - ' . $model->ref('product_sub_category_id')->getTitle();
};

$crud = Crud::addTo($app);
$crud->setModel($model, ['name']);
