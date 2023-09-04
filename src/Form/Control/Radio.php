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

    /** @var array<int|string, string> List of values. */
    public $values = [];

    protected function init(): void
    {
        parent::init();

        // radios are annoying because they don't send value when they are not ticked
        if ($this->form) {
            $this->form->onHook(Form::HOOK_LOAD_POST, function (Form $form, array &$postRawData) {
                if (!isset($postRawData[$this->shortName])) {
                    $postRawData[$this->shortName] = '';
                }
            });
        }

        $this->lister = Lister::addTo($this, [], ['Radio']);
        $this->lister->tRow->set('_name', $this->shortName);
    }

    protected function renderView(): void
    {
        if (!$this->model) {
            // we cannot use "id" column here as seeding Array_ persistence with 0 will throw "Must not be a zero"
            // $this->setSource($this->values);
            $this->setSource(array_map(static fn ($k, string $v) => ['k' => $k, 'name' => $v], array_keys($this->values), $this->values));
            $this->model->idField = 'k';
        }

        $value = $this->entityField ? $this->entityField->get() : $this->content;

        $this->lister->setModel($this->model);

        $this->lister->onHook(Lister::HOOK_BEFORE_ROW, function (Lister $lister) use ($value) {
            if ($this->disabled) {
                $lister->tRow->dangerouslySetHtml('disabledClass', 'disabled');
                $lister->tRow->dangerouslySetHtml('disabled', 'disabled="disabled"');
            } elseif ($this->readOnly) {
                $lister->tRow->dangerouslySetHtml('disabledClass', 'read-only');
                $lister->tRow->dangerouslySetHtml('disabled', 'readonly="readonly"');
            }

            $lister->tRow->set('value', $this->getApp()->uiPersistence->typecastSaveField($this->entityField->getField(), $lister->currentRow->getId()));

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
