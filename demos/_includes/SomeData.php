<?php

namespace atk4\ui\demo;

class SomeData extends \atk4\data\Model
{
    public function __construct()
    {
        $p = new Persistence_Faker();

        parent::__construct($p);
    }

    public function init(): void
    {
        parent::init();
        $m = $this;

        $m->addField('title');
        $m->addField('name');
        $m->addField('surname', ['actual' => 'name']);
        $m->addField('date', ['type' => 'date']);
        $m->addField('salary', ['type' => 'money', 'actual' => 'randomNumber']);
        $m->addField('logo_url');
    }
}
