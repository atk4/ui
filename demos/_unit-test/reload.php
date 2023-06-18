<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Callback;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\Loader;
use Atk4\Ui\ViewWithContent;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$v = ViewWithContent::addTo($app, ['ui' => 'segment']);
$v->set('Test');
$v->name = 'reload';

$b = Button::addTo($app)->set('Reload');
$b->on('click', new JsReload($v));

$cb = Callback::addTo($app);
$cb->setUrlTrigger('c_reload');

Loader::addTo($app, ['cb' => $cb])->set(function (Loader $p) {
    ViewWithContent::addTo($p, ['ui' => 'segment'])->set('loaded');
});
