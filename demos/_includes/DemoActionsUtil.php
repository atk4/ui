<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Model\UserAction;
use Atk4\Data\Persistence\Array_;
use Atk4\Ui\Form\Control\Dropdown;

/**
 *  @property string $age @Atk4\Field()
 *  @property string $city @Atk4\Field()
 *  @property string $gender @Atk4\Field()
 */
class ArgModel extends Model
{

    protected function init(): void
    {
        parent::init();
        $this->addField($this->fieldName()->age, ['type' => 'integer', 'required' => true, 'caption' => 'Age must be 21 or over:']);
        $this->addField($this->fieldName()->city);
        $this->addField($this->fieldName()->gender, [
            'default' => 'm',
            'ui' => ['form' => [Dropdown::class, 'values' => ['m' => 'Male', 'f' => 'Female']]
            ]
        ]);
    }

    public function validate(string $intent = null): array
    {
        $error = [];
        if ($this->get(ArgModel::hinting()->fieldName()->age) < 21) {
            $error = [ArgModel::hinting()->fieldName()->age => 'You must be at least 21 years old.'];
        }

        return array_merge($error, parent::validate($intent));
    }
}

class DemoActionsUtil
{
    public static function setupDemoActions(CountryLock $country): void
    {
        $country->addUserAction(
            'callback',
            [
                'description' => 'Callback',
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
                'description' => function (UserAction $action) {
                    return 'Edit ISO3 for country: ' . $action->getEntity()->getTitle();
                },
                'fields' => [$country->fieldName()->iso3],
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
                'confirmation' => function ($a) {
                    return 'Are you sure you want to perform this action on: <b>' . $a->getEntity()->getTitle() . ' (' . $a->getEntity()->iso3 . ')</b>';
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
                    'gender' => [
                        'type' => 'string',
                        'required' => true,
                        'default' => 'm',
                        'ui' => [
                            'form' => [
                                Dropdown::class, 'values' => ['m' => 'Male', 'f' => 'Female'],
                            ],
                        ],
                    ],
                ],
                'fields' => [$country->fieldName()->iso3],
                'callback' => function ($model, $age, $city, $gender) {
                    $n = $gender === 'm' ? 'Mr.' : 'Mrs.';

                    return 'Thank you ' . $n . ' at age ' . $age;
                },
                'preview' => function ($model, $age, $city, $gender) {
                    return 'Gender = ' . $gender . ' / Age = ' . $age;
                },
            ]
        );

        $country->addUserAction(
            'arg_using_model',
            [
                'caption' => 'Arg with Model',
                'description' => 'Ask for Arguments set via a Data\Model. Allow usage of model validate() for your arguments',
                'args' => [
                    '__atk_model' => new ArgModel(new Array_([])),
                    'extra' => ['type' => 'string'],
                ],
                'fields' => [$country->fieldName()->iso3],
                'callback' => function ($model, $age, $city, $gender) {
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
