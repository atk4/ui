<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model\UserAction;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;

class DemoActionsUtil
{
    public static function setupDemoActions(Country $country): void
    {
        $country->addUserAction('callback', [
            'description' => 'Callback',
            'callback' => static function (Country $entity) {
                return 'callback execute using country ' . $entity->getTitle();
            },
        ]);

        $country->addUserAction('preview', [
            'description' => 'Display Preview prior to run the action',
            'preview' => static function (Country $entity) {
                return 'Previewing country ' . $entity->getTitle();
            },
            'callback' => static function (Country $entity) {
                return 'Done previewing ' . $entity->getTitle();
            },
        ]);

        $country->addUserAction('disabled_action', [
            'description' => 'This action is disabled.',
            'caption' => 'Disabled',
            'enabled' => false,
            'callback' => static function () {
                return 'ok';
            },
        ]);

        $country->addUserAction('edit_argument', [
            'caption' => 'Argument',
            'description' => 'Ask for argument "Age" prior to execute the action.',
            'args' => [
                'age' => ['type' => 'integer', 'required' => true],
            ],
            'callback' => static function (Country $entity, int $age) {
                if ($age < 18) {
                    $text = 'Sorry not old enough to visit ' . $entity->getTitle();
                } else {
                    $text = $age . ' is old enough to visit ' . $entity->getTitle();
                }

                return $text;
            },
        ]);

        $country->addUserAction('edit_argument_preview', [
            'caption' => 'Argument/Preview',
            'description' => 'Ask for argument "Age" and display preview prior to execute',
            'args' => [
                'age' => ['type' => 'integer', 'required' => true],
            ],
            'preview' => static function (Country $entity, int $age) {
                return 'You age is: ' . $age;
            },
            'callback' => static function (Country $entity, $age) {
                return 'age = ' . $age;
            },
        ]);

        $country->addUserAction('edit_iso', [
            'caption' => 'Edit ISO3',
            'description' => static function (UserAction $action) {
                return 'Edit ISO3 for country: ' /* TODO . $action->getEntity()->getTitle() */;
            },
            'fields' => [$country->fieldName()->iso3],
            'callback' => static function () {
                return 'ok';
            },
        ]);

        $country->addUserAction('Ouch', [
            'caption' => 'Exception',
            'description' => 'Throw an exception when executing an action',
            'args' => [
                'age' => ['type' => 'integer'],
            ],
            'preview' => static function () {
                return 'Be careful with this action.';
            },
            'callback' => static function () {
                throw new Exception('Told you, didn\'t I?');
            },
        ]);

        $country->addUserAction('confirm', [
            'caption' => 'User Confirmation',
            'description' => 'Confirm the action using a ConfirmationExecutor',
            'confirmation' => static function (UserAction $action) {
                $iso3 = Country::assertInstanceOf($action->getEntity())->iso3;

                return 'Are you sure you want to perform this action on: <b>' . $action->getEntity()->getTitle() . ' (' . $iso3 . ')</b>';
            },
            'callback' => static function (Country $entity) {
                return 'Confirm country ' . $entity->getTitle();
            },
        ]);

        $country->addUserAction('multi_step', [
            'caption' => 'Multi Step',
            'description' => 'Ask for Arguments and field and display preview prior to run the action',
            'args' => [
                'age' => ['type' => 'integer', 'required' => true],
                'city' => [],
                'gender' => [
                    'type' => 'string',
                    'required' => true,
                    'default' => 'm',
                    'ui' => [
                        'form' => [
                            Form\Control\Dropdown::class, 'values' => ['m' => 'Male', 'f' => 'Female'],
                        ],
                    ],
                ],
            ],
            'fields' => [$country->fieldName()->iso3],
            'preview' => static function (Country $entity, int $age, string $city, string $gender) {
                return 'Gender = ' . $gender . ' / Age = ' . $age;
            },
            'callback' => static function (Country $entity, int $age, string $city, string $gender) {
                $n = $gender === 'm' ? 'Mr.' : 'Mrs.';

                return 'Thank you ' . $n . ' at age ' . $age;
            },
        ]);
    }
}
