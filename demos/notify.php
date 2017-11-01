<?php

require 'init.php';

$app->add(['Button', 'Notify Examples - Page 2', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['notify2']);

$app->add(['Button', 'Test'])->on('click', (new \atk4\ui\jsNotify('Not yet implemented'))->setColor('red'));

$modal = $app->add(['Modal', 'Modal Title']);

$modal->set(function ($p) use ($modal) {
    $form = $p->add('Form');
    $form->addField('name', null, ['caption'=>'Add your name']);

    $form->onSubmit(function ($f) use ($modal) {
        if (empty($f->model['name'])) {
            return $f->error('name', 'Please add a name!');
        } else {
            return [
                $modal->hide(),
                new \atk4\ui\jsNotify('Thank you '.$f->model['name'])
            ];
        }
    });
});

$app->add(['Button', 'Open Modal'])->on('click', $modal->show());

