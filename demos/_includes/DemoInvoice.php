<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/**
 * Invoice class for tutorial intro.
 */
class DemoInvoice extends \Atk4\Data\Model
{
    public $dateFormat;

    public $titleField = 'reference';

    protected function init(): void
    {
        parent::init();

        $this->addField('reference', ['required' => true]);
        $this->addField('date', [
            'type' => 'date',
            'required' => true,
        ]);
    }
}
