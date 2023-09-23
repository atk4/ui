<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Label;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// create header
Header::addTo($app, ['Database-driven form with an enjoyable layout']);

// create form
$form = Form::addTo($app, ['class.segment' => true]);
// $form = Form::addTo($app, ['class.segment' => true, 'buttonSave' => false]);
// $form = Form::addTo($app, ['class.segment' => true, 'buttonSave' => new Button(['Import', 'class.secondary' => true, 'iconRight' => 'list'])]);
// $form = Form::addTo($app, ['class.segment' => true, 'buttonSave' => [null, 'Import', 'class.secondary' => true, 'iconRight' => 'list']]);
Label::addTo($form, ['Input new country information here', 'class.top attached' => true], ['AboveControls']);

$form->setModel((new Country($app->db))->createEntity(), []);

// form basic field group
$formAddress = $form->addGroup('Basic Country Information');
$nameInput = $formAddress->addControl(Country::hinting()->fieldName()->name, ['width' => 'sixteen']);
$nameInput->addAction(['Check Duplicate', 'iconRight' => 'search'])
    ->on('click', static function (Jquery $jquery, string $name) use ($app, $form) {
        if ((new Country($app->db))->tryLoadBy(Country::hinting()->fieldName()->name, $name) !== null) {
            return $form->js()->form('add prompt', Country::hinting()->fieldName()->name, 'This country name is already added.');
        }

        return new JsToast('This country name can be added.');
    }, ['args' => [$nameInput->jsInput()->val()]]);

// form codes field group
$formCodes = $form->addGroup(['Codes']);
$formCodes->addControl(Country::hinting()->fieldName()->iso, ['width' => 'four'])->iconLeft = 'flag';
$formCodes->addControl(Country::hinting()->fieldName()->iso3, ['width' => 'four'])->iconLeft = 'flag';
$formCodes->addControl(Country::hinting()->fieldName()->numcode, ['width' => 'four'])->iconLeft = 'flag';
$formCodes->addControl(Country::hinting()->fieldName()->phonecode, ['width' => 'four'])->iconLeft = 'flag';

// form names field group
$formNames = $form->addGroup(['More Information about you']);
$formNames->addControl('first_name', ['width' => 'five', 'caption' => 'First Name']);
$formNames->addControl('middle_name', ['width' => 'five', 'caption' => 'Middle Name']);
$formNames->addControl('last_name', ['width' => 'six', 'caption' => 'Last Name']);

// form on submit
$form->onSubmit(static function (Form $form) {
    $countryEntity = (new Country($form->getApp()->db))->createEntity();
    // Model will have some validation too
    foreach ($form->model->getFields('editable') as $k => $field) {
        if ($countryEntity->hasField($k)) {
            $countryEntity->set($k, $form->model->get($k));
        }
    }

    // in-form validation
    $errors = [];
    if (mb_strlen($form->model->get('first_name')) < 3) {
        $errors[] = $form->jsError('first_name', 'too short, ' . $form->model->get('first_name'));
    }
    if (mb_strlen($form->model->get('last_name')) < 5) {
        $errors[] = $form->jsError('last_name', 'too short');
    }

    // Model validation. We do it manually because we are not using Model::save() method in demo mode.
    foreach ($countryEntity->validate('save') as $k => $error) {
        $errors[] = $form->jsError($k, $error);
    }

    if ($errors) {
        return new JsBlock($errors);
    }

    return new JsToast($countryEntity->getUserAction('add')->execute());
});

$personClass = AnonymousClassNameCache::get_class(fn () => new class() extends Model {
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

    public function validate(string $intent = null): array
    {
        $errors = parent::validate($intent);

        if ($this->get('name') === $this->get('surname')) {
            $errors['surname'] = 'Your surname cannot be same as the name';
        }

        return $errors;
    }
});

$form = Form::addTo($app)->addClass('segment');
$form->setModel((new $personClass($app->db))->createEntity());

$form->onSubmit(static function (Form $form) {
    return new JsToast('Form saved!');
});
