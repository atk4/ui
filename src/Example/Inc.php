<?php

declare(strict_types=1);

namespace Atk4\Ui\Example;

use Atk4\Data\Model;
use Atk4\Ui\Form;
use Atk4\Ui\UserAction\ModalExecutor;

class Inc
{
}

class CustomUserAction extends Model\UserAction
{
    /** @var array<string, mixed> */
    public $ui;
}

class CustomForm extends Form
{
    protected function init(): void
    {
        parent::init();

        // demo - allow custom modal form executor to be recognized easily
        $this->style['padding'] = '10px';
        $this->style['background-color'] = '#ccf';
    }

    public function addControl(string $name, $control = [], $field = []): Form\Control
    {
        // demo - handle self::addControl() calls
        // the calls are made by StepExecutorTrait::doArgs(), the result is is unused there,
        // but you should create the form controls and place/add them via custom logic to desired places/layouts
        return $this->layout->addControl($name, $control, $field);
    }
}

class CustomModalExecutor extends ModalExecutor
{
}
