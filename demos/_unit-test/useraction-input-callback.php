<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model\UserAction;
use Atk4\Ui\App;
use Atk4\Ui\Form;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$country = new Country($app->db);

$country->addUserAction('greetInteger', [
    'appliesTo' => UserAction::APPLIES_TO_NO_RECORD,
    'args' => [
        'foo' => [
            'type' => 'integer',
            'required' => true,
        ],
    ],
    'callback' => static function (Country $entity, int $foo) {
        return 'Hello II ' . $foo;
    },
]);

Form\Control\Line::addTo($app, ['action' => $country->getUserAction('greetInteger')]);

$country->addUserAction('greetWrappedId', [
    'appliesTo' => UserAction::APPLIES_TO_NO_RECORD,
    'args' => [
        'foo' => [
            'type' => WrappedIdType::NAME,
            'required' => true,
        ],
    ],
    'callback' => static function (Country $entity, WrappedId $foo) {
        return 'Hello III ' . $foo->getId();
    },
]);

Form\Control\Line::addTo($app, ['action' => $country->getUserAction('greetWrappedId')]);
