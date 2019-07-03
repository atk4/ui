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
     */
    public function initPreview()
    {
        $this->addHeader();

        if (!$this->form) {
            $this->form = $this->add('Form');
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
     * Return field from model.
     *
     * @param Model $model
     *
     * @return array
     */
    protected function getModelFields(Model $model)
    {
        $fields = [];
        foreach ($model->elements as $f) {
            if (!$f instanceof Field) {
                continue;
            }

            if ($f->isEditable() || $f->isVisible()) {
                $fields[] = $f->short_name;
            }
        }

        return $fields;
    }
}
