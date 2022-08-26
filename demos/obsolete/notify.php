<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\JsNotify;
use Atk4\Ui\Modal;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Notify Examples - Page 2', 'class.small right floated basic blue' => true, 'iconRight' => 'right arrow'])
    ->link(['notify2']);

Button::addTo($app, ['Test'])->on('click', (new JsNotify('Not yet implemented'))->setColor('red'));

$modal = Modal::addTo($app, ['Modal Title']);

$modal->set(function ($p) use ($modal) {
    $form = Form::addTo($p);
    $form->addControl('name', [], ['caption' => 'Add your name']);

    $form->onSubmit(function (Form $form) use ($modal) {
        if (!$form->model->get('name')) {
            return $form->error('name', 'Please add a name!');
        }

        return [
            $modal->hide(),
            new JsNotify('Thank you ' . $form->model->get('name')),
        ];
    });
});

Button::addTo($app, ['Open Modal'])->on('click', $modal->show());
