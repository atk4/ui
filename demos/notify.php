<?php

require 'init.php';
require 'database.php';

class Notifier extends \atk4\data\Model
{
    public $table = 'notifier';
    public function init()
    {
        parent::init();
        $this->addField('text', [
            'default' => 'This text will appear in notification',
            'caption' => 'type any text'
        ]);
        $this->addField('icon', [
            'default' => 'warning sign',
            'caption' => 'Use semantic-ui icon name'
        ]);
        $this->addField('type', [
            'enum'    => ['success', 'info', 'warning', 'error'],
            'default' => 'success',
            'caption' => 'Select type: '
        ]);
        $this->addField('transition', [
            'enum'    => ['scale', 'fade', 'jiggle', 'flash'],
            'default' => 'scale',
            'caption' => 'Select transition:'
        ]);
        $this->addField('width', [
            'enum'    => ['25%', '50%', '75%', '100%'],
            'default' => '100%' ,
            'caption' => 'Select width:'
        ]);
        $this->addField('position', [
            'enum'    => ['topLeft', 'topCenter', 'topRight', 'bottomLeft', 'bottomCenter', 'bottomRight', 'center'],
            'default' => 'topCenter',
            'caption' => 'Select position:'
        ]);
        $this->addField('attach', [
            'enum'    => ['Body', 'Form'],
            'default' => 'Body',
            'caption' => 'Attach to:'
        ]);
    }
}

$head = $layout->add(['Header', 'Notification Types']);

$form = $layout->add(new \atk4\ui\Form(['segment']));
$form->add(['Label', 'Some of notification preferences you can set.', 'top attached'], 'AboveFields');
$form->buttonSave->set('Show');
$form->setModel(new Notifier($db), false);

$f_p = $form->addGroup(['Set Text and Icon:']);
$f_p->addField('text', ['width'=>'eight']);
$f_p->addField('icon', ['width'=>'four']);

$f_p1 = $form->addGroup(['Set Type, Transition and Width:']);
$f_p1->addField('type', ['width'=>'four']);
$f_p1->addField('transition', ['width'=>'four']);
$f_p1->addField('width', ['width'=>'four']);

$f_p2 = $form->addGroup(['Set Position and Attach to:']);
$f_p2->addField('position', ['width'=>'four']);
$f_p2->addField('attach', ['width'=>'four']);

$form->onSubmit(function ($f) {

    $options = [
        'type'           => $f->model['type'],
        'position'       => $f->model['position'],
        'width'          => $f->model['width'],
        'openTransition' => $f->model['transition'],
        'icon'           => $f->model['icon'],
        'content'        => $f->model['text']
    ];

    if ($f->model['attach'] === 'Body') {
        $chain = new \atk4\ui\jsChain();
    } else {
        $chain = $f->js();
    }

    return $chain->atkNotify($options);
});
