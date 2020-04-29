<?php
/**
 * Invoice class for tutorial intro.
 */
class AtkDemoInvoice extends \atk4\data\Model
{
    public $title_field = 'reference';

    public function init(): void
    {
        parent::init();

        $this->addField('reference');
        $this->addField('date', ['type' => 'date']);
    }
}
