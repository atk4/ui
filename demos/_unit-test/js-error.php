<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\JsExpression;
use Atk4\Ui\Modal;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$modal = Modal::addTo($app);

$modal->set(function (View $p) {
    $p->js(true, new JsExpression('$(\'<div />\').modal({onShow: () => true})'));
});

$button = Button::addTo($app)->set('Test');
$button->on('click', $modal->jsShow());
