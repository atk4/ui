<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

class Notifier extends \atk4\data\Model
{
    public $table = 'notifier';

    public function init(): void
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
}

 // Notification type form
$head = \atk4\ui\Header::addTo($app, ['Notification Types']);

$form = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($form, ['Some of notification options that can be set.', 'top attached'], ['AboveFields']);
$form->buttonSave->set('Show');
$form->setModel(new Notifier($db), false);

$f_p = $form->addGroup(['Set Text and Icon:']);
$f_p->addField('text', ['width' => 'eight']);
$f_p->addField('icon', ['width' => 'four']);

$f_p1 = $form->addGroup(['Set Color, Transition and Width:']);
$f_p1->addField('color', ['width' => 'four']);
$f_p1->addField('transition', ['width' => 'four']);
$f_p1->addField('width', ['width' => 'four']);

$f_p2 = $form->addGroup(['Set Position and Attach to:']);
$f_p2->addField('position', ['width' => 'four']);
$f_p2->addField('attach', ['width' => 'four']);

$form->onSubmit(function (\atk4\ui\Form $form) {
    $notifier = new \atk4\ui\jsNotify();
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
