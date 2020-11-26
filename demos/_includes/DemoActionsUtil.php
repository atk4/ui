<?php

declare(strict_types=1);

namespace atk4\ui\demo;

class DemoActionsUtil
{
    public static function setupDemoActions(CountryLock $country): void
    {
        $country->addUserAction(
            'callback',
            ['description' => 'Callback',
                'callback' => function ($model) {
                    return 'callback execute using country ' . $model->getTitle();
                },
            ]
        );

        $country->addUserAction(
            'preview',
            [
                'description' => 'Preview',
                'preview' => function ($model) {
                    return 'Previewing country ' . $model->getTitle();
                },
                'callback' => function ($model) {
                    return 'Done previewing ' . $model->getTitle();
                },
            ]
        );

        $country->addUserAction(
            'disabled_action',
            [
                'description' => 'Disabled',
                'enabled' => false,
                'callback' => function () {
                    return 'ok';
                },
            ]
        );

        $country->addUserAction(
            'edit_argument',
            [
                'description' => 'Argument',
                'args' => [
                    'age' => ['type' => 'integer', 'required' => true],
                ],
                'callback' => function ($model, $age) {
                    if ($age < 18) {
                        $text = 'Sorry not old enough to visit ' . $model->getTitle();
                    } else {
                        $text = $age . ' is old enough to visit ' . $model->getTitle();
                    }

                    return $text;
                },
            ]
        );

        $country->addUserAction(
            'edit_argument_prev',
            [
                'description' => 'Argument/Preview',
                'args' => ['age' => ['type' => 'integer', 'required' => true]],
                'preview' => function ($model, $age) {
                    return 'You age is: ' . $age;
                },
                'callback' => function ($model, $age) {
                    return 'age = ' . $age;
                },
            ]
        );

        $country->addUserAction(
            'edit_iso',
            [
                'description' => 'Edit ISO3 only',
                'fields' => ['iso3'],
                'callback' => function () {
                    return 'ok';
                },
            ]
        );

        $country->addUserAction(
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

        $country->addUserAction(
            'confirm',
            [
                'caption' => 'Confirm action ',
                'description' => 'User Confirmation',
                'ui' => ['executor' => [\atk4\ui\UserAction\ConfirmationExecutor::class]],
                'confirmation' => function ($a) {
                    return 'Are you sure you want to perform this action on: <b>' . $a->getModel()->getTitle() . ' (' . $a->getModel()->get('iso3') . ')</b>';
                },
                'callback' => function ($model) {
                    return 'Confirm country ' . $model->getTitle();
                },
            ]
        );

        $country->addUserAction(
            'multi_step',
            [
                'description' => 'Argument/Field/Preview',
                'args' => [
                    'age' => ['type' => 'integer', 'required' => true],
                    'city' => [],
                    'gender' => ['type' => 'enum', 'values' => ['m' => 'Male', 'f' => 'Female'], 'required' => true, 'default' => 'm'],
                ],
                'fields' => ['iso3'],
                'callback' => function ($model, $age, $city, $gender) {
                    //    $model->save();
                    $n = $gender === 'm' ? 'Mr.' : 'Mrs.';

                    return 'Thank you ' . $n . ' at age ' . $age;
                },
                'preview' => function ($model, $age, $city, $gender) {
                    return 'Gender = ' . $gender . ' / Age = ' . $age;
                },
            ]
        );
    }
}
