<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Button;
use Atk4\Ui\Crud;
use Atk4\Ui\Exception;
use Atk4\Ui\Header;
use Atk4\Ui\Loader;
use Atk4\Ui\UserAction\ExecutorFactory;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$m = (new Country($app->db))->setLimit(5);
$app->getExecutorFactory()->registerTrigger(
    ExecutorFactory::TABLE_BUTTON,
    [Button::class, 'ui' => 'atk-test button', 'icon' => 'pencil'],
    $m->getUserAction('edit')
);

$loader = Loader::addTo($app, ['loadEvent' => false]);
$loader->cb->setUrlTrigger('trigger_main_loader');

$loader->set(static function (Loader $p) use ($m) {
    Header::addTo($p, ['Loader-1', 'size' => 4]);

    if ($p->getApp()->hasRequestQueryParam('err_main_loader')) {
        throw new Exception('Exception from Main Loader');
    }

    $loaderSub = Loader::addTo($p, ['loadEvent' => false]);
    $loaderSub->cb->setUrlTrigger('trigger_sub_loader');

    $loaderSub->set(static function (Loader $p) use ($m) {
        Header::addTo($p, ['Loader-2', 'size' => 4]);

        if ($p->getApp()->hasRequestQueryParam('err_sub_loader')) {
            throw new Exception('Exception from Sub Loader');
        } elseif ($p->getApp()->hasRequestQueryParam('err_sub_loader2')) {
            throw new \Error('Exception II from Sub Loader');
        }

        $loaderSubSub = Loader::addTo($p);

        $loaderSubSub->set(static function (Loader $p) use ($m) {
            Header::addTo($p, ['Loader-3', 'size' => 4]);

            $c = Crud::addTo($p, ['ipp' => 4]);
            $c->setModel($m, [$m->fieldName()->name]);
        });
    });
    Button::addTo($p, ['Load2'])
        ->on('click', $loaderSub->jsLoad());
});

Button::addTo($app, ['Load1'])
    ->on('click', $loader->jsLoad());
