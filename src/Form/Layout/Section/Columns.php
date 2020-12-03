<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout\Section;

/**
 * Represents form controls in columns.
 */
class Columns extends \Atk4\Ui\Columns
{
    public $formLayout = \Atk4\Ui\Form\Layout::class;
    public $form;

    /**
     * Add new vertical column.
     *
     * @param int|array $defaults specify width (1..16) or relative to $width
     *
     * @return \Atk4\Ui\Form\Layout
     */
    public function addColumn($defaults = null)
    {
        $column = parent::addColumn($defaults);

        return $column->add([$this->formLayout, 'form' => $this->form]);
    }
}
