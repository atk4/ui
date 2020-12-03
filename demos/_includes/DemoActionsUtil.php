<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

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
                'description' => 'Display Preview prior to run the action',
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
                'description' => 'This action is disabled.',
                'caption' => 'Disabled',
                'enabled' => false,
                'callback' => function () {
                    return 'ok';
                },
            ]
        );

        $country->addUserAction(
            'edit_argument',
            [
                'caption' => 'Argument',
                'description' => 'Ask for argument "Age" prior to execute the action.',
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
                'caption' => 'Argument/Preview',
                'description' => 'Ask for argument "Age" and display preview prior to execute',
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
                'caption' => 'Edit ISO3',
                'description' => function ($action) {
                    return 'Edit ISO3 for country: ' . $action->getModel()->getTitle();
                },
                'fields' => ['iso3'],
                'callback' => function () {
                    return 'ok';
                },
            ]
        );

        $country->addUserAction(
            'Ouch',
            [
                'caption' => 'Exception',
                'description' => 'Throw an exception when executing an action',
                'args' => ['age' => ['type' => 'integer']],
                'preview' => function () {
                    return 'Be careful with this action.';
                },
                'callback' => function () {
                    throw new \Atk4\Ui\Exception('Told you, didn\'t I?');
                },
            ]
        );

        $country->addUserAction(
            'confirm',
            [
                'caption' => 'User Confirmation',
                'description' => 'Confirm the action using a ConfirmationExecutor',
                'ui' => ['executor' => [\Atk4\Ui\UserAction\ConfirmationExecutor::class]],
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
                'caption' => 'Multi Step',
                'description' => 'Ask for Arguments and field and display preview prior to run the action',
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
