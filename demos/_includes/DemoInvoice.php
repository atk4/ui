<?php

namespace atk4\ui\demo;

/**
 * Invoice class for tutorial intro.
 */
class DemoInvoice extends \atk4\data\Model
{
    public $title_field = 'reference';

    public function init(): void
    {
        parent::init();

        $this->addField('reference');
        $this->addField('date', ['type' => 'date']);
    }
}
