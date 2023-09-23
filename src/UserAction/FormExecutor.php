<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Data\Model;
use Atk4\Ui\Form;

class FormExecutor extends BasicExecutor
{
    /** @var Form|null */
    public $form;

    /**
     * If form model is not set then will use action fields property to set model field.
     * If action fields property is empty then will use all model fields as default.
     *
     * If model is already supply in form, then editable fields must match action fields property.
     */
    public function initPreview(): void
    {
        $this->addHeader();

        if (!$this->form) {
            $this->form = Form::addTo($this);
        }

        // setup form model using action fields
        if (!$this->form->model) {
            if (!$this->action->fields) {
                $this->action->fields = $this->getModelFields($this->action->getModel());
            }
            $this->form->setModel($this->action->getEntity(), $this->action->fields);
        }

        $this->form->onSubmit(function (Form $form) {
            return $this->executeModelAction();
        });
    }

    /**
     * Returns array of names of fields.
     * This includes all editable or visible fields of the model.
     *
     * @return array
     */
    protected function getModelFields(Model $model)
    {
        return array_keys($model->getFields(['editable', 'visible']));
    }
}
