<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Form;
use Atk4\Ui\JsExpression;
use Atk4\Ui\Lister;

/**
 * Input element for a form control.
 */
class Radio extends Form\Control
{
    public $ui = false;

    public $defaultTemplate = 'form/control/radio.html';

    /** @var Lister Contains a lister that will render individual radio buttons. */
    public $lister;

    /** @var array List of values. */
    public $values = [];

    protected function init(): void
    {
        parent::init();

        $this->lister = Lister::addTo($this, [], ['Radio']);
        $this->lister->tRow->set('_name', $this->shortName);
    }

    protected function renderView(): void
    {
        if (!$this->model) {
            $this->setSource($this->values);
        }

        $value = $this->entityField ? $this->entityField->get() : $this->content;

        $this->lister->setModel($this->model);

        if ($this->disabled) {
            $this->addClass('disabled');
        }

        $this->lister->onHook(Lister::HOOK_BEFORE_ROW, function (Lister $lister) use ($value) {
            if ($this->readOnly) {
                $lister->tRow->set('disabled', $value !== (string) $lister->model->getId() ? 'disabled="disabled"' : '');
            } elseif ($this->disabled) {
                $lister->tRow->set('disabled', 'disabled="disabled"');
            }

            $lister->tRow->set('checked', $value === (string) $lister->model->getId() ? 'checked' : '');
        });

        parent::renderView();
    }

    public function onChange($expr, $defaults = []): void
    {
        if (is_string($expr)) {
            $expr = new JsExpression($expr);
        }

        if (is_bool($defaults)) {
            $defaults = $defaults ? [] : ['preventDefault' => false, 'stopPropagation' => false];
        }

        $this->on('change', 'input', $expr, $defaults);
    }
}
