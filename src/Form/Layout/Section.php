<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout;

/**
 * Form generic layout section.
 */
class Section extends \Atk4\Ui\View
{
    public $formLayout = \Atk4\Ui\Form\Layout::class;
    public $form;

    /**
     * Adds sub-layout in existing layout.
     *
     * @return \Atk4\Ui\Form\Layout
     */
    public function addSection()
    {
        return $this->add([$this->formLayout, 'form' => $this->form]);
    }
}
