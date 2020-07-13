<?php

declare(strict_types=1);

namespace atk4\ui\Form\Layout;

/**
 * Form generic layout section.
 */
class Section extends \atk4\ui\View
{
    public $formLayout = \atk4\ui\Form\Layout::class;
    public $form;

    /**
     * Adds sub-layout in existing layout.
     *
     * @return \atk4\ui\Form\Layout
     */
    public function addSection()
    {
        return $this->add([$this->formLayout, 'form' => $this->form]);
    }
}
