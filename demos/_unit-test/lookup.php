<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Crud;

// Test for hasOne Lookup as dropdown control.

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new ProductLock($app->db);
$model->addCondition('name', '=', 'Mustard');

$edit = $model->getUserAction('edit');
$edit->ui = ['execButton' => [\Atk4\Ui\Button::class, 'EditMe', 'blue']];
$edit->description = 'edit';
$edit->callback = function ($model) {
    return $model->ref('product_category_id')->getTitle() . ' - ' . $model->ref('product_sub_category_id')->getTitle();
};

$crud = Crud::addTo($app);
$crud->setModel($model, ['name']);
