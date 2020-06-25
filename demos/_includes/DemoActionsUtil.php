<?php

declare(strict_types=1);

namespace atk4\ui\demo;

class DemoActionsUtil
{
    public static function setupDemoActions(CountryLock $country): void
    {
        $country->addAction(
            'callback',
            ['description' => 'Callback',
                'callback' => function ($m) {
                    return 'callback execute using country ' . $m->getTitle();
                },
            ]
        );

        $country->addAction(
            'preview',
            [
                'description' => 'Preview',
                'preview' => function ($m) {
                    return 'Previewing country ' . $m->getTitle();
                },
                'callback' => function ($m) {
                    return 'Done previewing ' . $m->getTitle();
                },
            ]
        );

        $country->addAction(
            'disabled_action',
            [
                'description' => 'Disabled',
                'enabled' => false,
                'callback' => function () {
                    return 'ok';
                },
            ]
        );

        $country->addAction(
            'edit_argument',
            [
                'description' => 'Argument',
                'args' => [
                    'age' => ['type' => 'integer', 'required' => true],
                ],
                'callback' => function ($m, $age) {
                    if ($age < 18) {
                        $text = 'Sorry not old enough to visit ' . $m->getTitle();
                    } else {
                        $text = $age . ' is old enough to visit ' . $m->getTitle();
                    }

                    return $text;
                },
            ]
        );

        $country->addAction(
            'edit_argument_prev',
            [
                'description' => 'Argument/Preview',
                'args' => ['age' => ['type' => 'integer', 'required' => true]],
                'preview' => function ($m, $age) {
                    return 'You age is: ' . $age;
                },
                'callback' => function ($m, $age) {
                    return 'age = ' . $age;
                },
            ]
        );

        $country->addAction(
            'edit_iso',
            [
                'description' => 'Edit ISO3 only',
                'fields' => ['iso3'],
                'callback' => function () {
                    return 'ok';
                },
            ]
        );

        $country->addAction(
            'Ouch',
            [
                'description' => 'Exception',
                'args' => ['age' => ['type' => 'integer']],
                'preview' => function () {
                    return 'Be careful with this action.';
                },
                'callback' => function () {
                    throw new \atk4\ui\Exception('Told you, didn\'t I?');
                },
            ]
        );

        $country->addAction(
            'confirm',
            [
                'caption' => 'Confirm action ',
                'description' => 'User Confirmation',
                'ui' => ['executor' => [\atk4\ui\ActionExecutor\UserConfirmation::class]],
                'confirmation' => function ($a) {
                    return 'Are you sure you want to perform this action on: <b>' . $a->getModel()->getTitle() . ' (' . $a->getModel()->get('iso3') . ')</b>';
                },
                'callback' => function ($m) {
                    return 'Confirm country ' . $m->getTitle();
                },
            ]
        );

        $country->addAction(
            'multi_step',
            [
                'description' => 'Argument/Field/Preview',
                'args' => [
                    'age' => ['type' => 'integer', 'required' => true],
                    'city' => [],
                    'gender' => ['type' => 'enum', 'values' => ['m' => 'Male', 'f' => 'Female'], 'required' => true, 'default' => 'm'],
                ],
                'fields' => ['iso3'],
                'callback' => function ($m, $age, $city, $gender) {
                    //    $m->save();
                    $n = $gender === 'm' ? 'Mr.' : 'Mrs.';

                    return 'Thank you ' . $n . ' at age ' . $age;
                },
                'preview' => function ($m, $age, $city, $gender) {
                    return 'Gender = ' . $gender . ' / Age = ' . $age;
                },
            ]
        );
    }
}
