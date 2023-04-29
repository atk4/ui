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
     * Add new vertical column.
     *
     * @param int|array $defaults specify width (1..16) or relative to $width
     *
     * @return Form\Layout
     */
    public function addColumn($defaults = [])
    {
        $column = parent::addColumn($defaults);

        return $column->add([$this->formLayout, 'form' => $this->form]); // @phpstan-ignore-line
    }
}
