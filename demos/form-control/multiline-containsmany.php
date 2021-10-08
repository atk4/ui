<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form\Control\Multiline;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// This demo require specific Database setup.

if (!class_exists(Client::class)) {
    class Client extends ModelWithPrefixedFields
    {
        public $table = 'client';
        public $caption = 'Client';

        protected function init(): void
        {
            parent::init();

            $this->addField('name');
            $this->containsMany('accounts' /* TODO "Accounts" was here, but tests are failing for PostgreSQL, different casing should be supported */ , ['model' => [Account::class]]);
        }
    }

    class Account extends ModelWithPrefixedFields
    {
        public $caption = ' ';

        protected function init(): void
        {
            parent::init();

            $this->addField('email', [
                'required' => true,
                'ui' => ['multiline' => [Multiline::INPUT => ['icon' => 'envelope', 'type' => 'email']]],
            ]);
            $this->addField('password', [
                'required' => true,
                'ui' => ['multiline' => [Multiline::INPUT => ['icon' => 'key', 'type' => 'password']]],
            ]);
            $this->addField('site', ['required' => true]);
            $this->addField('type', [
                'default' => 'user',
                'values' => ['user' => 'Regular User', 'admin' => 'System Admin'],
                'ui' => ['multiline' => [Multiline::TABLE_CELL => ['width' => 'four']]],
            ]);
        }
    }
}

\Atk4\Ui\Crud::addTo($app)->setModel(new Client($app->db));
