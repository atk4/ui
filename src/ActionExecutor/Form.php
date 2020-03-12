<?php
/**
 * Action form executor.
 */

namespace atk4\ui\ActionExecutor;

use atk4\data\Field;
use atk4\data\Model;

class Form extends Basic
{
    /**
     * @var \atk4\ui\Form
     */
    public $form = null;

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
            $this->form = $this->add('Form');
        }

        // Setup form model using action fields.
        if (!$this->form->model) {
            if (!$this->action->fields) {
                $this->action->fields = $this->getModelFields($this->action->owner);
            }
            $this->form->setModel($this->action->owner, $this->action->fields);
        }

        $this->form->onSubmit(function ($f) {
            return $this->jsExecute();
        });
    }

    /**
     * Returns array of names of fields.
     * This includes all editable or visible fields of the model.
     *
     * @param \atk4\data\Model $model
     *
     * @return array
     */
    protected function getModelFields(Model $model)
    {
        $fields = [];
        foreach ($model->getFields() as $f) {
            if ($f->isEditable() || $f->isVisible()) {
                $fields[] = $f->short_name;
            }
        }

        return $fields;
    }
}
