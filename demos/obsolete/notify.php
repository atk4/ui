<?php

require_once __DIR__ . '/../atk-init.php';

\atk4\ui\Button::addTo($app, ['Notify Examples - Page 2', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['notify2']);

\atk4\ui\Button::addTo($app, ['Test'])->on('click', (new \atk4\ui\jsNotify('Not yet implemented'))->setColor('red'));

$modal = \atk4\ui\Modal::addTo($app, ['Modal Title']);

$modal->set(function ($p) use ($modal) {
    $form = \atk4\ui\Form::addTo($p);
    $form->addField('name', null, ['caption' => 'Add your name']);

    $form->onSubmit(function ($f) use ($modal) {
        if (empty($f->model->get('name'))) {
            return $f->error('name', 'Please add a name!');
        }

        return [
            $modal->hide(),
            new \atk4\ui\jsNotify('Thank you ' . $f->model->get('name')),
        ];
    });
});

\atk4\ui\Button::addTo($app, ['Open Modal'])->on('click', $modal->show());
