<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

/**
 * Demonstrates how to use Breadcrumb.
 */
$crumb = \Atk4\Ui\Breadcrumb::addTo($app);
$crumb->addCrumb('UI Demo', ['index']);
$crumb->addCrumb('Breadcrumb Demo', ['breadcrumb']);

\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);

$crumb->addCrumb('Countries', []);

$model = new CountryLock($app->db);
$model->setLimit(15);

if ($id = $app->stickyGet('country_id')) {
    // perhaps we edit individual country?
    $model->load($id);
    $crumb->addCrumb($model->get('name'), []);

    // here we can check for additional criteria and display a deeper level on the crumb

    $form = \Atk4\Ui\Form::addTo($app);
    $form->setModel($model);
    $form->onSubmit(function (\Atk4\Ui\Form $form) {
        return new \Atk4\Ui\JsToast('Form Submitted! Data saving is not possible in demo!');
    });
} else {
    // display list of countries
    $table = \Atk4\Ui\Table::addTo($app);
    $table->setModel($model);
    $table->addDecorator('name', [\Atk4\Ui\Table\Column\Link::class, [], ['country_id' => 'id']]);
}

$crumb->popTitle();
