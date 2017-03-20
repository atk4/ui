<?php
/**
 * Testing form.
 */
require 'init.php';
require 'database.php';

$layout->add(['Header', 'Database-driven form with an enjoyable layout']);

$form = $layout->add(new \atk4\ui\Form(['segment']));

$form->setModel(new Country($db), false)->loadBy('iso', 'GB');

$f_address = $form->addGroup('Basic Country Information');
$f_address->addField('name', ['width'=>'ten'])
    ->addAction(['Check Duplicate', 'iconRight'=>'search'])
    ->on('click', function ($val) {
        // We can't get the value until https://github.com/atk4/ui/issues/77
        return 'Value appears to be unique';
    });

$f_address->addField('iso', ['Post Code', 'width'=>'three'])->iconLeft = 'flag';
$f_address->addField('iso3', ['Post Code', 'width'=>'three'])->iconLeft = 'flag';

$f_guardian = $form->addGroup(['Codes', 'inline'=>true]);
$f_guardian->addField('first_name', ['width'=>'eight']);

$f_guardian->addField('middle_name', ['width'=>'three', 'disabled'=>true]);
$f_guardian->addField('last_name', ['width'=>'five']);

$form->onSubmit(function ($f) {
    $errors = [];
    if (strlen($f->model['first_name']) < 3) {
        $errors[] = $f->error('first_name', 'too short, '.$f->model['first_name']);
    }
    if (strlen($f->model['last_name']) < 5) {
        $errors[] = $f->error('last_name', 'too short');
    }

    if ($errors) {
        return $errors;
    }

    $f->model->save();

    return $f->success(
        'Record Added',
        'there are now '.$f->model->action('count')->getOne().' records in DB'
    );
});
