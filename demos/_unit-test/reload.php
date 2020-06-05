<?php

namespace atk4\ui\demo;

use atk4\ui\Button;
use atk4\ui\Callback;
use atk4\ui\jsReload;
use atk4\ui\View;

require_once __DIR__ . '/../atk-init.php';

$v = View::addTo($app, ['ui' => 'segment']);
$v->set('Test');
$v->name = 'reload';

$b = Button::addTo($app)->set('Reload');
$b->on('click', new jsReload($v));

$cb = Callback::addTo($app);
$cb->urlTrigger = 'c_reload';

\atk4\ui\Loader::addTo($app, ['cb' => $cb])->set(function ($page) {
    $v = View::addTo($page, ['ui' => 'segment'])->set('loaded');
});
