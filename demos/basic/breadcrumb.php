<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Breadcrumb;
use Atk4\Ui\Form;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Table;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$crumb = Breadcrumb::addTo($app);
$crumb->addCrumb('UI Demo', '..');
$crumb->addCrumb('Basics', '.');
$crumb->addCrumb('Breadcrumb Demo', ['breadcrumb']);

View::addTo($app, ['ui' => 'divider']);

$crumb->addCrumb('Countries', []);

$model = new Country($app->db);
$model->setLimit(15);

$id = $crumb->stickyGet('country_id');
if ($id) {
    // perhaps we edit individual country?
    $model = $model->load($id);
    $crumb->addCrumb($model->name, []);

    // here we can check for additional criteria and display a deeper level on the crumb

    $form = Form::addTo($app);
    $form->setModel($model);
    $form->onSubmit(static function (Form $form) {
        return new JsToast('Form Submitted! Data saving is not possible in demo!');
    });
} else {
    // display list of countries
    $table = Table::addTo($app);
    $table->setModel($model);
    $table->addDecorator($model->fieldName()->name, [Table\Column\Link::class, [], ['country_id' => $model->fieldName()->id]]);
}

$crumb->popTitle();
