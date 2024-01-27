<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout\Section;

use Atk4\Ui\Columns as UiColumns;
use Atk4\Ui\Form;
use Atk4\Ui\View;

/**
 * Represents form controls in columns.
 */
class Columns extends UiColumns
{
    /** @var array */
    public $formLayoutSeed = [Form\Layout::class];

    public Form $form;

    /**
     * @return Form\Layout
     */
    #[\Override]
    public function addColumn($defaults = [])
    {
        $column = parent::addColumn($defaults);

        $res = View::fromSeed($this->formLayoutSeed, ['form' => $this->form]);
        $column->add($res);

        return $res;
    }
}
