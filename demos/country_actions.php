<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$country = new Country($db);
$ct = $country->tryLoadAny();
$id = $ct->get('id');
$country_name = $ct->getTitle();

$c_actions = [];

$c_actions['ac_cb'] = $country->addAction(
    'callback',
    [   'description' => 'Callback',
        'callback'=> function ($m) {
            return 'callback execute using country ' . $m->getTitle();
        }
    ]
);

$c_actions['ac_preview'] = $country->addAction(
    'preview',
    [
        'description' => 'Preview',
        'preview' => function ($m) {
            return 'Previewing country ' . $m->getTitle();
        },
        'callback' => function ($m) {
            return 'Done previewing ' . $m->getTitle();
        }
    ]
);

$c_actions['ac_disabled'] = $country->addAction(
    'disabled_action',
    [
        'description' => 'Disabled',
        'enabled' => false,
        'callback' => function () {
            return 'ok';
        }
    ]
);

$c_actions['ac_edit_arg'] = $country->addAction(
    'edit_argument',
    [
        'description' => 'Argument',
        'args' => [
            'age' => ['type' => 'integer', 'required' => true]
        ],
        'callback' => function ($m, $age) {
            if ($age < 18) {
                $text = 'Sorry not old enough to visit ' . $m->getTitle();
            } else {
                $text = $age . ' is old enough to visit ' . $m->getTitle();
            }
            return $text;
        }
    ]
);

$c_actions['ac_edit_arg_preview'] = $country->addAction(
    'edit_argument_prev',
    [
        'description' => 'Argument/Preview',
        'args' => ['age'=>['type'=>'integer', 'required' => true]],
        'preview' => function ($m, $age) {
            return 'You age is: ' . $age;
        },
        'callback' => function ($m, $age) {
            return 'age = ' . $age;
        }
    ]
);

$c_actions['ac_edit_iso'] = $country->addAction(
    'edit_iso',
    [
        'description' => 'Edit ISO3 only',
        'fields' => ['iso3'],
        'callback' => function () {
            return 'ok';
        }
    ]
);

$c_actions['ac_ouch'] = $country->addAction(
    'Ouch',
    [
        'description' => 'Exception',
        'args' => ['age' => ['type' => 'integer']],
        'preview'=> function () {
            return 'Be careful with this action.';
        },
        'callback'=> function () {
            throw new \atk4\ui\Exception('Told you, didn\'t I?');
        }
    ]
);

$c_actions['ac_confirm'] = $country->addAction(
    'confirm',
    [
        'description' => 'Confirm',
        'ui' => ['executor' => \atk4\ui\ActionExecutor\UserConfirmation::class],
        'confirmation' => function ($m) {
          return 'test';
        },
        'callback' => function ($m) {
            return 'Confirm ok ' . $m->getTitle();
        }
    ]
);

$c_actions['ac_multi'] = $country->addAction(
    'multi_step',
    [
        'description' => 'Argument/Field/Preview',
        'args' => [
            'age' => ['type' => 'integer', 'required' => true],
            'gender' => ['type' => 'enum', 'values' => ['m' => 'Male', 'f' => 'Female'], 'required' => true],
        ],
        'fields' => ['iso3'],
        'callback'=> function ($m, $age, $gender) {
            //    $m->save();
            return 'ok';
        },
        'preview' => function ($m, $age, $gender) {
            return 'Gender = ' . $gender . ' / Age = ' . $age . ' / ' . $m->get('iso3');
        }
    ]
);
