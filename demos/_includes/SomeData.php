<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

class SomeData extends \Atk4\Data\Model
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
