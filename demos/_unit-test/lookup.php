<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Crud;
use Atk4\Ui\UserAction\ExecutorFactory;

// test hasOne Lookup as dropdown control

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Product($app->db);
$model->addCondition($model->fieldName()->name, '=', 'Mustard');

$app->getExecutorFactory()->useTriggerDefault(ExecutorFactory::TABLE_BUTTON);

$edit = $model->getUserAction('edit');
$edit->callback = static function (Product $model) {
    return $model->product_category_id->getTitle() . ' - ' . $model->product_sub_category_id->getTitle();
};

$crud = Crud::addTo($app);
$crud->setModel($model, [$model->fieldName()->name]);
