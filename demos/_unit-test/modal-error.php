<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Modal;
use Atk4\Ui\UserAction\ModalExecutor;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$modal = Modal::addTo($app);
$modal->set(static function (View $p) {
    $modal = Modal::addTo($p);
    $modal->set(static function (View $p) {
        throw new \Error('Exception from Modal');
    });
    $button = Button::addTo($p)->set('Test Modal load PHP error');
    $button->on('click', $modal->jsShow());

    $modal = Modal::addTo($p);
    $modal->set(static function (View $p) {
        $p->js(true, new JsExpression('$(\'<div />\').modal({onShow: () => true})'));
    });
    $button = Button::addTo($p)->set('Test Modal load JS error');
    $button->on('click', $modal->jsShow());

    $country = new Country($p->getApp()->db);
    $button = Button::addTo($p)->set('Test ModalExecutor load PHP error');
    $executor = ModalExecutor::assertInstanceOf($p->getExecutorFactory()->createExecutor($country->getUserAction('edit'), $button));
    if (\Closure::bind(static fn () => $executor->loader, null, ModalExecutor::class)()->cb->isTriggered()) {
        $executor->stickyGet($executor->name, '-1');
    }
    $button->on('click', $executor);
});
$button = Button::addTo($app)->set('Test');
$button->on('click', $modal->jsShow());
