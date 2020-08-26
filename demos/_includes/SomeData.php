<?php

declare(strict_types=1);

namespace atk4\ui\demo;

class SomeData extends \atk4\data\Model
{
    public function __construct()
    {
        $fakerPersistence = new Persistence_Faker();

        parent::__construct($fakerPersistence);
    }

    protected function init(): void
    {
        parent::init();
        $model = $this;

        $model->addField('title');
        $model->addField('name');
        $model->addField('surname', ['actual' => 'name']);
        $model->addField('date', ['type' => 'date']);
        $model->addField('salary', ['type' => 'money', 'actual' => 'randomNumber']);
        $model->addField('logo_url');
    }
}
