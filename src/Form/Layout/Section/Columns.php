<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout\Section;

use Atk4\Ui\Columns as UiColumns;
use Atk4\Ui\Form;

/**
 * Represents form controls in columns.
 */
class Columns extends UiColumns
{
    /** @var class-string<Form\Layout> */
    public $formLayout = Form\Layout::class;

    public Form $form;

    /**
     * @return Form\Layout
     */
    #[\Override]
    public function addColumn($defaults = [])
    {
        $column = parent::addColumn($defaults);

        return $column->add([$this->formLayout, 'form' => $this->form]); // @phpstan-ignore-line
    }
}
