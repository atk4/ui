<?php

declare(strict_types=1);

namespace atk4\ui\Form\Layout\Section;

/**
 * Represents form controls in columns.
 */
class Columns extends \atk4\ui\Columns
{
    public $formLayout = \atk4\ui\Form\Layout::class;
    public $form;

    /**
     * Add new vertical column.
     *
     * @param int|array $defaults specify width (1..16) or relative to $width
     *
     * @return \atk4\ui\Form\Layout
     */
    public function addColumn($defaults = null)
    {
        $column = parent::addColumn($defaults);

        return $column->add([$this->formLayout, 'form' => $this->form]);
    }
}
