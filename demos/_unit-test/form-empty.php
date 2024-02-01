<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Form;
use Atk4\Ui\Js\JsToast;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$form = Form::addTo($app);

$form->onSubmit(static function (Form $form) {
    return new JsToast('Post ' . ($form->getApp()->getRequest()->getParsedBody() === [] ? 'ok' : 'unexpected'));
});
