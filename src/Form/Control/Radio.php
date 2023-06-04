<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Form;
use Atk4\Ui\Lister;

class Radio extends Form\Control
{
    public $ui = false;
    public array $class = ['grouped', 'fields'];

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

        $this->lister->onHook(Lister::HOOK_BEFORE_ROW, function (Lister $lister) use ($value) {
            if ($this->disabled) {
                $lister->tRow->dangerouslySetHtml('disabledClass', ' disabled');
                $lister->tRow->dangerouslySetHtml('disabled', 'disabled="disabled"');
            } elseif ($this->readOnly) {
                $lister->tRow->dangerouslySetHtml('disabledClass', ' read-only');
                $lister->tRow->dangerouslySetHtml('disabled', 'readonly="readonly"');
            }

            $lister->tRow->dangerouslySetHtml('checked', $lister->model->compare($lister->model->idField, $value) ? 'checked="checked"' : '');
        });

        $this->js(true, null, '.ui.checkbox.radio')->checkbox([
            'uncheckable' => !$this->entityField || ($this->entityField->getField()->nullable || !$this->entityField->getField()->required),
        ]);

        parent::renderView();
    }

    public function onChange($expr, $defaults = []): void
    {
        if (is_bool($defaults)) {
            $defaults = $defaults ? [] : ['preventDefault' => false, 'stopPropagation' => false];
        }

        $this->on('change', 'input', $expr, $defaults);
    }
}
