<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;

class Flyers extends Model
{
    protected function init(): void
    {
        parent::init();

        $this->addField('first_name');
        $this->addField('last_name');
        $this->addField('age', ['values' => [1 => 'From months to 2 years old', 2 => 'From 3 to 17 years old', 3 => '18 years or more']]);
    }
}
