<?php

namespace atk4\ui\demo;

use atk4\ui\Form;
use atk4\ui\jsToast;

require_once __DIR__ . '/../atk-init.php';

$f = Form::addTo($app);
$f->name = 'test_form';

$f->addField('f1')->set('v1');

$f->onSubmit(function ($f) {
    return new jsToast('Post ok');
});
