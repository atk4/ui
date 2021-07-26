<?php

declare(strict_types=1);
/**
 * Behat testing.
 * Test for callback in callback.
 */

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Crud;
use Atk4\Ui\Header;
use Atk4\Ui\Loader;
use Atk4\Ui\UserAction\ExecutorFactory;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$m = (new CountryLock($app->db))->setLimit(5);
$app->getExecutorFactory()->registerTrigger(
    ExecutorFactory::TABLE_BUTTON,
    [Button::class, 'ui' => 'atk-test button', 'icon' => 'pencil'],
    $m->getUserAction('edit')
);

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
            $c->setModel($m, [$m->fieldName()->name]);
        });
    });
    \Atk4\Ui\Button::addTo($p, ['Load2'])->js('click', $loader_1->jsLoad());
});

\Atk4\Ui\Button::addTo($app, ['Load1'])->js('click', $loader->jsLoad());
