<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;
use atk4\ui\JsToast;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$form = Form::addTo($app);
$form->cb->setUrlTrigger('test_submit');

$form->addControl('f1')->set('v1');

$form->onSubmit(function ($form) {
    return new JsToast('Post ok');
});
