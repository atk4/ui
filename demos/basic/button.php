<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Header;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Basic Button', 'size' => 2]);

$form = Form::addTo($app);
$recipient = $form->addControl(
    'recipient',
    [Form\Control\Dropdown::class,
        'values' => [],
        'isMultiple' => true,
        'dropdownOptions' => ['allowAdditions' => true, 'forceSelection' => false],
    ],
    ['default' => 'Username <user@emaildomain.de>']
);
$form->onSubmit(static function () use ($form) {
    echo $form->getApp()->getTag('pre', [], print_r($form->model->get(), true));
});
