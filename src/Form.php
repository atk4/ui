<?php

namespace atk4\ui;

/**
 * Implements a form.
 */
class Form extends View implements \ArrayAccess
{
    public $ui = 'form';

    public $defaultTemplate = 'form.html';

    public $layout = null;

    public function addField(...$args)
    {
        if (!$this->model) {
            $this->model = new \atk4\ui\misc\ProxyModel();
        }

        $this->model->addField(...$args);
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Provided with a Agile Data Model Field, this method have to decide
     * and create instance of a View that will act as a form-field.
     *
     * @return Form\Field\Generic
     */
    public function fieldFactory(\atk4\data\Field $f)
    {
        return new FormField\Line($this, $f);
    }

    /**
     * Associates form with the model but also specifies which of Model
     * fields should be added automatically.
     *
     * If $actualFields are not specified, then all "editable" fields
     * will be added.
     */
    public function setModel(\atk4\data\Model $model, $fields = null)
    {
        $model = parent::setModel($model);

        // Will not try to populate any fields
        if ($fields === false) {
            return $model;
        }

        if ($fields === null) {
            // TODO: $fields = $model->getFields('editable');
        } elseif (is_array($fields)) {
            foreach ($fields as $field) {
                $modelField = $model->getElement($field);

                $formField = $this->add($this->fieldFactory($modelField));
            }
        } else {
            throw new Exception(['Incorrect value for $fields', 'fields'=>$fields]);
        }
    }

    public function init()
    {
        parent::init();
    }
}
