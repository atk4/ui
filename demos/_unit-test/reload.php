<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Callback;
use Atk4\Ui\JsReload;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$v = View::addTo($app, ['ui' => 'segment']);
$v->set('Test');
$v->name = 'reload';

$b = Button::addTo($app)->set('Reload');
$b->on('click', new JsReload($v));

$cb = Callback::addTo($app);
$cb->setUrlTrigger('c_reload');

\Atk4\Ui\Loader::addTo($app, ['cb' => $cb])->set(function ($page) {
    $v = View::addTo($page, ['ui' => 'segment'])->set('loaded');
});
