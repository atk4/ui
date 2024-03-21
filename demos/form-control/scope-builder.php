<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Crud;
use Atk4\Ui\Form;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Modal;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Stat($app->db, ['caption' => 'Demo Stat']);

$crud = Crud::addTo($app);
$crud->setModel($model);

$modal = Modal::addTo($app);
$form = Form::addTo($modal);

/** @var Form\Control\ScopeBuilder The scopebuilder */
$qb = $form->addControl('qb', [Form\Control\ScopeBuilder::class, 'model' => $model,
    'options' => ['addAllReferencedKeys' => true, 'debug' => true]]);

if ($filter = $crud->stickyGet('filter')) {
    $model->addCondition($qb->queryToScope($app->decodeJson($filter)));
}

$form->onSubmit(static function (Form $form) use ($modal, $crud) {
    return new JsBlock([
        $crud->jsReload(['filter' => $form->model->get('qb')]),
        $modal->jsHide(),
    ]);
});

$crud->menu->addItem(['icon' => 'filter'], $modal->jsShow());
