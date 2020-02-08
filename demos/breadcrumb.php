<?php
/**
 * Demonstrates how to use BreadCrumb.
 */
require __DIR__ . '/init.php';
require __DIR__ . '/database.php';

$crumb = $app->add('BreadCrumb');
$crumb->addCrumb('UI Demo', ['index']);
$crumb->addCrumb('BreadCrumb Demo', ['breadcrumb']);

$app->add(['ui'=>'divider']);

$crumb->addCrumb('Countries', []);

$m = new Country($db);

if ($id = $app->stickyGet('country_id')) {

    // perhaps we edit individual country?
    $m->load($id);
    $crumb->addCrumb($m['name'], []);

    // here we can check for additional criteria and display a deeper level on the crumb

    $app->add('Form')->setModel($m);
} else {

    // display list of countries
    $table = $app->add('Table');
    $table->setModel($m);
    $table->addDecorator('name', ['Link', [], ['country_id'=>'id']]);
}

$crumb->popTitle();
