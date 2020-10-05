<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Button;
use atk4\ui\Callback;
use atk4\ui\JsReload;
use atk4\ui\View;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$v = View::addTo($app, ['ui' => 'segment']);
$v->set('Test');
$v->name = 'reload';

$b = Button::addTo($app)->set('Reload');
$b->on('click', new JsReload($v));

$cb = Callback::addTo($app);
$cb->setUrlTrigger('c_reload');

\atk4\ui\Loader::addTo($app, ['cb' => $cb])->set(function ($page) {
    $v = View::addTo($page, ['ui' => 'segment'])->set('loaded');
});
