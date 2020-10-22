<?php

declare(strict_types=1);
/**
 * Behat testing.
 * Test for hasOne Lookup as dropdown control.
 */

namespace atk4\ui\demo;

use atk4\ui\Crud;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new ProductLock($app->db);
$model->addCondition('name', '=', 'Mustard');

$edit = $model->getUserAction('edit');
$edit->ui = ['execButton' => [\atk4\ui\Button::class, 'EditMe', 'blue']];
$edit->description = 'edit';
$edit->callback = function ($model) use ($app) {
    return $model->ref('product_category_id')->getTitle() . ' - ' . $model->ref('product_sub_category_id')->getTitle();
};

$crud = Crud::addTo($app);

// Properly set control for Behat test.
$crud->onFormEdit(function ($f) {
    $f->getControl('product_category_id')->settings['duration'] = 0;
    $f->getControl('product_category_id')->settings['delay'] = ['hide' => 0, 'search' => 0];
    // Important to reduce control width because it could cause Behat test to fail if over EditMe button.
    $f->getControl('product_category_id')->setStyle(['width' => '50%']);

    $f->getControl('product_sub_category_id')->settings['duration'] = 0;
    $f->getControl('product_sub_category_id')->settings['delay'] = ['hide' => 0, 'search' => 0];
    // Important to reduce control width because it could cause Behat test to fail if over EditMe button.
    $f->getControl('product_sub_category_id')->setStyle(['width' => '50%']);
});

$crud->setModel($model, ['name']);
