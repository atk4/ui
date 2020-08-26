<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

/** @var \atk4\data\Model $notifierClass */
$notifierClass = get_class(new class() extends \atk4\data\Model {
    public $table = 'notifier';

    protected function init(): void
    {
        parent::init();

        $this->addField('text', ['default' => 'This text will appear in notification', 'caption' => 'type any text']);
        $this->addField('icon', ['default' => 'warning sign', 'caption' => 'Use semantic-ui icon name']);
        $this->addField('color', ['enum' => ['green', 'red', 'orange', 'yellow', 'teal', 'blue', 'violet', 'purple', 'pink', 'brown'], 'default' => 'green', 'caption' => 'Select color:']);
        $this->addField('transition', ['enum' => ['scale', 'fade', 'jiggle', 'flash'], 'default' => 'jiggle', 'caption' => 'Select transition:']);
        $this->addField('width', ['enum' => ['25%', '50%', '75%', '100%'], 'default' => '25%', 'caption' => 'Select width:']);
        $this->addField('position', ['enum' => ['topLeft', 'topCenter', 'topRight', 'bottomLeft', 'bottomCenter', 'bottomRight', 'center'], 'default' => 'topRight', 'caption' => 'Select position:']);
        $this->addField('attach', ['enum' => ['Body', 'Form'], 'default' => 'Body', 'caption' => 'Attach to:']);
    }
});

 // Notification type form
$head = \atk4\ui\Header::addTo($app, ['Notification Types']);

$form = \atk4\ui\Form::addTo($app, ['segment']);
// Unit test only.
$form->cb->setUrlTrigger('test_notify');

\atk4\ui\Label::addTo($form, ['Some of notification options that can be set.', 'top attached'], ['AboveControls']);
$form->buttonSave->set('Show');
$form->setModel(new $notifierClass($app->db), false);

$formGroup = $form->addGroup(['Set Text and Icon:']);
$formGroup->addControl('text', ['width' => 'eight']);
$formGroup->addControl('icon', ['width' => 'four']);

$formGroup1 = $form->addGroup(['Set Color, Transition and Width:']);
$formGroup1->addControl('color', ['width' => 'four']);
$formGroup1->addControl('transition', ['width' => 'four']);
$formGroup1->addControl('width', ['width' => 'four']);

$formGroup2 = $form->addGroup(['Set Position and Attach to:']);
$formGroup2->addControl('position', ['width' => 'four']);
$formGroup2->addControl('attach', ['width' => 'four']);

$form->onSubmit(function (\atk4\ui\Form $form) {
    $notifier = new \atk4\ui\JsNotify();
    $notifier->setColor($form->model->get('color'))
        ->setPosition($form->model->get('position'))
        ->setWidth(rtrim($form->model->get('width'), '%'))
        ->setContent($form->model->get('text'))
        ->setTransition($form->model->get('transition'))
        ->setIcon($form->model->get('icon'));

    if ($form->model->get('attach') !== 'Body') {
        $notifier->attachTo($form);
    }

    return $notifier;
});
