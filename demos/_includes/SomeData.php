<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;

class SomeData extends Model
{
    public function __construct()
    {
        $fakerPersistence = new FakerPersistence();

        parent::__construct($fakerPersistence);
    }

    protected function init(): void
    {
        parent::init();

        $this->addField('title');
        $this->addField('name');
        $this->addField('surname', ['actual' => 'name']);
        $this->addField('date', ['type' => 'date']);
        $this->addField('salary', ['type' => 'atk4_money', 'actual' => 'randomNumber']);
        $this->addField('logo_url');
    }
}
