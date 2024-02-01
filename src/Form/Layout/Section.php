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
    /** @var array */
    public $formLayoutSeed = [Form\Layout::class];

    public Form $form;

    /**
     * Adds sub-layout in existing layout.
     *
     * @return Form\Layout
     */
    public function addSection()
    {
        $res = View::fromSeed($this->formLayoutSeed, ['form' => $this->form]);
        $this->add($res);

        return $res;
    }
}
