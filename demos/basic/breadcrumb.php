<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

/**
 * Demonstrates how to use BreadCrumb.
 */
$crumb = \atk4\ui\BreadCrumb::addTo($app);
$crumb->addCrumb('UI Demo', ['index']);
$crumb->addCrumb('BreadCrumb Demo', ['breadcrumb']);

\atk4\ui\View::addTo($app, ['ui' => 'divider']);

$crumb->addCrumb('Countries', []);

$m = new CountryLock($db);
$m->setLimit(15);

if ($id = $app->stickyGet('country_id')) {
    // perhaps we edit individual country?
    $m->load($id);
    $crumb->addCrumb($m->get('name'), []);

    // here we can check for additional criteria and display a deeper level on the crumb

    $f = \atk4\ui\Form::addTo($app);
    $f->setModel($m);
    $f->onSubmit(function (\atk4\ui\Form $form) {
        return new \atk4\ui\jsToast('Form Submitted! Data saving is not possible in demo!');
    });
} else {
    // display list of countries
    $table = \atk4\ui\Table::addTo($app);
    $table->setModel($m);
    $table->addDecorator('name', [\atk4\ui\TableColumn\Link::class, [], ['country_id' => 'id']]);
}

$crumb->popTitle();
