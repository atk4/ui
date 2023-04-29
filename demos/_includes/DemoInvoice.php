<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;

/**
 * Invoice class for tutorial intro.
 */
class DemoInvoice extends Model
{
    public ?string $titleField = 'reference';

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
