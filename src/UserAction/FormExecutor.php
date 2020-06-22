<?php

declare(strict_types=1);

namespace atk4\ui\UserAction;

use atk4\data\Model;
use atk4\ui\Form;

class FormExecutor extends BasicExecutor
{
    /**
     * @var Form
     */
    public $form;

    /**
     * Initialization.
     * If form model is not set then will use action fields property to set model field.
     * If action fields property is empty then will use all model fields as default.
     *
     * If model is already supply in form, then editable fields must match action fields property.
     */
    public function initPreview()
    {
        $this->addHeader();

        if (!$this->form) {
            $this->form = Form::addTo($this);
        }

        // Setup form model using action fields.
        if (!$this->form->model) {
            if (!$this->action->fields) {
                $this->action->fields = $this->getModelFields($this->action->owner);
            }
            $this->form->setModel($this->action->owner, $this->action->fields);
        }

        $this->form->onSubmit(function (Form $form) {
            return $this->jsExecute();
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
