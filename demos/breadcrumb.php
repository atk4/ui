<?php
/**
 * Demonstrates how to use BreadCrumb.
 */
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$crumb = \atk4\ui\BreadCrumb::addTo($app);
$crumb->addCrumb('UI Demo', ['index']);
$crumb->addCrumb('BreadCrumb Demo', ['breadcrumb']);

\atk4\ui\View::addTo($app, ['ui'=>'divider']);

$crumb->addCrumb('Countries', []);

$m = new Country($db);

if ($id = $app->stickyGet('country_id')) {

    // perhaps we edit individual country?
    $m->load($id);
    $crumb->addCrumb($m['name'], []);

    // here we can check for additional criteria and display a deeper level on the crumb

    \atk4\ui\Form::addTo($app)->setModel($m);
} else {

    // display list of countries
    $table = \atk4\ui\Table::addTo($app);
    $table->setModel($m);
    $table->addDecorator('name', ['Link', [], ['country_id'=>'id']]);
}

$crumb->popTitle();
