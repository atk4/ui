<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// This demo require specific Database setup.

class Client extends \atk4\data\Model
{
    public $table = 'client';
    public $caption = 'Client';

    protected function init(): void
    {
        parent::init();

        $data = [];

        $this->addField('name');
        $this->containsMany('Accounts', [Account::class]);
    }
}

class Account extends \atk4\data\Model
{
    public $caption = ' ';

    protected function init(): void
    {
        parent::init();

        $this->addField('email', ['required' => true, 'ui' => ['multiline' => ['input', ['icon' => 'envelope', 'type' => 'email']]]]);
        $this->addField('password', ['required' => true, 'ui' => ['multiline' => ['input', ['icon' => 'key', 'type' => 'password']]]]);
        $this->addField('site', ['required' => true]);
        $this->addField('type', ['default' => 'user', 'values' => ['user' => 'Regular User', 'admin' => 'System Admin'], 'ui' => ['multiline' => ['width' => 'four']]]);
    }
}

\atk4\ui\Crud::addTo($app)->setModel(new Client($app->db));
