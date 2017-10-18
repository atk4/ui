<?php

require 'init.php';
require 'database.php';

class Notifier extends \atk4\data\Model
{
    public $table = 'notifier';

    public function init()
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

 /*** Notification type form ****/
$head = $layout->add(['Header', 'Notification Types']);

$form = $layout->add(new \atk4\ui\Form(['segment']));
$form->add(['Label', 'Some of notification options that can be set.', 'top attached'], 'AboveFields');
$form->buttonSave->set('Show');
$form->setModel(new Notifier($db), false);

$f_p = $form->addGroup(['Set Text and Icon:']);
$f_p->addField('text', ['width'=>'eight']);
$f_p->addField('icon', ['width'=>'four']);

$f_p1 = $form->addGroup(['Set Color, Transition and Width:']);
$f_p1->addField('color', ['width'=>'four']);
$f_p1->addField('transition', ['width'=>'four']);
$f_p1->addField('width', ['width'=>'four']);

$f_p2 = $form->addGroup(['Set Position and Attach to:']);
$f_p2->addField('position', ['width'=>'four']);
$f_p2->addField('attach', ['width'=>'four']);

$form->onSubmit(function ($f) {
    $notifier = new \atk4\ui\jsNotify();
    $notifier->setColor($f->model['color'])
             ->setPosition($f->model['position'])
             ->setWidth(rtrim($f->model['width'], '%'))
             ->setContent($f->model['text'])
             ->setTransition($f->model['transition'])
             ->setIcon($f->model['icon']);

    if ($f->model['attach'] !== 'Body') {
        $notifier->attachTo($f);
    }

    return $notifier;
});

/**** Notification in modal with form ***/

$modal_form = $layout->add(['Modal', 'title'=>'Add a name']);

$modal_form->set(function ($page) use ($modal_form) {
    $a = [];
    $m = new \atk4\data\Model(new \atk4\data\Persistence_Array($a));
    $m->addField('name', ['caption'=>'Add your name']);

    $f = $page->add(new \atk4\ui\Form(['segment'=>true]));
    $f->setModel($m);

    $f->onSubmit(function ($f) use ($modal_form) {
        if (empty($f->model['name'])) {
            return $f->error('name', 'Please add a name!');
        } else {
            $js_actions[0] = $modal_form->hide();
            $js_actions[1] = new \atk4\ui\jsNotify([
                'position'       => 'topCenter',
                'content'        => 'Thank you '.$f->model['name'],
                'openTransition' => 'jiggle',
            ]);

            return $js_actions;
        }
    });
});

//Bind display modal to page display button.
$menu_bar = $layout->add(['View', 'ui'=>'buttons']);
$b = $menu_bar->add('Button')->set('On Modal Close');
$b->on('click', $modal_form->show());
