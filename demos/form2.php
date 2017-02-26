<?php
/**
 * Testing form.
 */
require 'init.php';

class Person extends \atk4\data\Model
{
    public $table = 'person';

    public function init()
    {
        parent::init();

        $this->addField('name');
        $this->addField('email');
        $this->addField('is_subscribed', ['type'=>'boolean', 'ui'=>['caption'=>'Subscribe to our Monthly Newsletter']]);
    }
}

$a = [];
$db = new \atk4\data\Persistence_Array($a);

$layout->add(new \atk4\ui\View([
    'Forms below focus on Data integration and automated layouts',
    'ui'=> 'ignored warning message',
]));

$layout->add(new \atk4\ui\Header('Fully-interractive, responsive and slick-looking form in 20 lines of PHP code', 'size'=>2));

$form = $layout->add(new \atk4\ui\Form(['segment']));

$form->addHeader('Fields with correct types can be imported from Domain Model');
$form->setModel(new Person($db));

$form->addHeader('Good controll over standard layouts');
$f_address = $form->addGroup('Address with label');
$f_address->addField('address', ['width'=>'twelve'])->iconLeft = 'building';
$f_address->addField('code', ['Post Code', 'width'=>'four']);

$f_guardian = $form->addGroup(['Guardian', 'inline'=>true]);
$f_guardian->addField('first_name', ['width'=>'eight'])
    ->action = ['Select', 'rightIcon'=>'search'];

$f_guardian->addField('middle_name', ['width'=>'three', 'disabled'=>true]);
$f_guardian->addField('last_name', ['width'=>'five']);

$form->onSubmit(function ($f) {
    $errors = [];
    if (strlen($f['first_name'] < 3)) {
        $errors[] = $f->error('first_name', 'too short');
    }
    if (strlen($f['last_name'] < 5)) {
        $errors[] = $f->error('last_name', 'too short');
    }

    if ($errors) {
        return $errors;
    }

    // create all related DB records
    $f->model->save();
    $f->model->ref('address_id')->save($f->get(['address', 'code']));
    $f->model->ref('Guardian')->insert($f->get(['first_name', 'middle_name', 'last_name']));

    return $f->success(
        'Record Added',
        'there are now '.$f->model->action('count')->getOne().' records in DB'
    );
});
