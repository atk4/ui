<?php

declare(strict_types=1);
/**
 * Behat testing.
 * Test for callback in callback.
 */

namespace atk4\ui\demo;

use atk4\ui\Button;
use atk4\ui\Crud;
use atk4\ui\Header;
use atk4\ui\Loader;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$m = (new CountryLock($app->db))->setLimit(5);
$m->getUserAction('edit')->ui['button'] = new Button(['Edit', ['ui' => 'atk-test button']]);

$loader = Loader::addTo($app);
$loader->loadEvent = false;

$loader->set(function ($p) use ($m) {
    $loader_1 = Loader::addTo($p);
    $loader_1->loadEvent = false;

    Header::addTo($p, ['Loader-1', 'size' => 4]);

    $loader_1->set(function ($p) use ($m) {
        Header::addTo($p, ['Loader-2', 'size' => 4]);
        $loader_3 = Loader::addTo($p);

        $loader_3->set(function ($p) use ($m) {
            Header::addTo($p, ['Loader-3', 'size' => 4]);

            $c = Crud::addTo($p, ['ipp' => 4]);
            $c->setModel($m, ['name']);
        });
    });
    \atk4\ui\Button::addTo($p, ['Load2'])->js('click', $loader_1->jsLoad());
});

\atk4\ui\Button::addTo($app, ['Load1'])->js('click', $loader->jsLoad());
