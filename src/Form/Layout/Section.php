<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout;

use Atk4\Ui\Form;
use Atk4\Ui\View;

/**
 * Form generic layout section.
 */
class Section extends View
{
    /** @var class-string<Form\Layout> */
    public $formLayout = Form\Layout::class;

    public Form $form;

    /**
     * Adds sub-layout in existing layout.
     *
     * @return Form\Layout
     */
    public function addSection()
    {
        return $this->add([$this->formLayout, 'form' => $this->form]);
    }
}
