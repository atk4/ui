<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/**
 * Invoice class for tutorial intro.
 */
class DemoInvoice extends \atk4\data\Model
{
    public $dateFormat;

    public $title_field = 'reference';

    protected function init(): void
    {
        parent::init();

        $this->addField('reference', ['required' => true]);
        $this->addField('date', [
            'type' => 'date',
            'required' => true,
            'typecast' => [
                function ($v) {
                    return ($v instanceof \DateTime) ? date_format($v, $this->dateFormat) : $v;
                }
            ],
        ]);
    }
}
