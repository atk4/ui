<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Testing form.

// create header
\Atk4\Ui\Header::addTo($app, ['Database-driven form with an enjoyable layout']);

// create form
$form = Form::addTo($app, ['segment']);
//$form = Form::addTo($app, ['segment', 'buttonSave'=>false]);
//$form = Form::addTo($app, ['segment', 'buttonSave'=>new \Atk4\Ui\Button(['Import', 'secondary', 'iconRight'=>'list'])]);
//$form = Form::addTo($app, ['segment', 'buttonSave'=>[null, 'Import', 'secondary', 'iconRight'=>'list']]);
\Atk4\Ui\Label::addTo($form, ['Input new country information here', 'top attached'], ['AboveControls']);

$form->setModel((new Country($app->db))->createEntity(), false);

// form basic field group
$formAddress = $form->addGroup('Basic Country Information');
$formAddress->addControl('name', ['width' => 'sixteen'])
    ->addAction(['Check Duplicate', 'iconRight' => 'search'])
    ->on('click', function ($val) {
        // We can't get the value until https://github.com/atk4/ui/issues/77
        return 'Value appears to be unique';
    });

// form codes field group
$formCodes = $form->addGroup(['Codes']);
$formCodes->addControl('iso', ['width' => 'four'])->iconLeft = 'flag';
$formCodes->addControl('iso3', ['width' => 'four'])->iconLeft = 'flag';
$formCodes->addControl('numcode', ['width' => 'four'])->iconLeft = 'flag';
$formCodes->addControl('phonecode', ['width' => 'four'])->iconLeft = 'flag';

// form names field group
$formNames = $form->addGroup(['More Information about you']);
$formNames->addControl('first_name', ['width' => 'eight']);
$formNames->addControl('middle_name', ['width' => 'three']);
$formNames->addControl('last_name', ['width' => 'five']);

// form on submit
$form->onSubmit(function (Form $form) {
    // In-form validation
    $errors = [];
    if (mb_strlen($form->model->get('first_name')) < 3) {
        $errors[] = $form->error('first_name', 'too short, ' . $form->model->get('first_name'));
    }
    if (mb_strlen($form->model->get('last_name')) < 5) {
        $errors[] = $form->error('last_name', 'too short');
    }
    if ($form->model->isDirty('iso')) { // restrict to change iso field value
        $errors[] = $form->error('iso', 'Field value should not be changed');
    }

    if ($errors) {
        return $errors;
    }

    // Model will have some validation too
    $form->model->save();

    return $form->success(
        'Record Added',
        'there are now ' . $form->model->action('count')->getOne() . ' records in DB'
    );
});

// ======

/** @var \Atk4\Data\Model $personClass */
$personClass = get_class(new class() extends \Atk4\Data\Model {
    public $table = 'person';

    protected function init(): void
    {
        parent::init();
        $this->addField('name', ['required' => true]);
        $this->addField('surname', ['ui' => ['placeholder' => 'e.g. Smith']]);
        $this->addField('gender', ['enum' => ['M', 'F']]);
        $this->hasOne('country_lookup_id', ['model' => [Country::class]]); // this works fast
        $this->hasOne('country_dropdown_id', ['model' => [Country::class], 'ui' => ['form' => new Form\Control\Dropdown()]]); // this works slow
    }

    public function validate($intent = null): array
    {
        $errors = parent::validate();

        if ($this->get('name') === $this->get('surname')) {
            $errors['surname'] = 'Your surname cannot be same as the name';
        }

        return $errors;
    }
});

Form::addTo($app)
    ->addClass('segment')
    ->setModel((new $personClass($app->db))->createEntity());
