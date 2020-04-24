<?php

/***
 * This demo require spefic Database setup.
 */

chdir('..');
require_once 'atk-init.php';

class Client extends atk4\data\Model
{
    public $table = 'client';
    public $caption = 'Client';

    public function init(): void
    {
        parent::init();

        $data = [];

        $this->addField('name');
        $this->containsMany('Accounts', [Account::class]);
    }
}

class Account extends atk4\data\Model
{
    public $caption = ' ';

    public function init(): void
    {
        parent::init();

        $this->addField('email', ['required' => true, 'ui' => ['multiline' => ['input', ['icon' => 'envelope', 'type' => 'email']]]]);
        $this->addField('password', ['required' => true, 'ui' => ['multiline' => ['input', ['icon' => 'key', 'type' => 'password']]]]);
        $this->addField('site', ['required' => true]);
        $this->addField('type', ['default' => 'user', 'values' => ['user' => 'Regular User', 'admin' => 'System Admin'], 'ui' => ['multiline' => ['width' => 'four']]]);
    }
}

\atk4\ui\CRUD::addTo($app)->setModel(new Client($db));
