<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Js\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Creation of request from globals depenmds on getallheaders
// which is not available in CLI mode
if (\PHP_SAPI === 'cli') {
    $app->setDefaults([
        'request' => $app->getRequest()->withParsedBody($_POST),
    ]);
}

$form = Form::addTo($app);
$form->cb->setUrlTrigger('test_submit');

$form->addControl('f1')->set('v1');

$form->onSubmit(static function (Form $form) {
    return new JsToast('Post ok');
});
