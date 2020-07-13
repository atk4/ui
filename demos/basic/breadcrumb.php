<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

/**
 * Demonstrates how to use Breadcrumb.
 */
$crumb = \atk4\ui\Breadcrumb::addTo($app);
$crumb->addCrumb('UI Demo', ['index']);
$crumb->addCrumb('Breadcrumb Demo', ['breadcrumb']);

\atk4\ui\View::addTo($app, ['ui' => 'divider']);

$crumb->addCrumb('Countries', []);

$model = new CountryLock($app->db);
$model->setLimit(15);

if ($id = $app->stickyGet('country_id')) {
    // perhaps we edit individual country?
    $model->load($id);
    $crumb->addCrumb($model->get('name'), []);

    // here we can check for additional criteria and display a deeper level on the crumb

    $form = \atk4\ui\Form::addTo($app);
    $form->setModel($model);
    $form->onSubmit(function (\atk4\ui\Form $form) {
        return new \atk4\ui\JsToast('Form Submitted! Data saving is not possible in demo!');
    });
} else {
    // display list of countries
    $table = \atk4\ui\Table::addTo($app);
    $table->setModel($model);
    $table->addDecorator('name', [\atk4\ui\Table\Column\Link::class, [], ['country_id' => 'id']]);
}

$crumb->popTitle();
