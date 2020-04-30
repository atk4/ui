<?php

namespace atk4\ui\FormLayout;

use atk4\ui\Exception;

/**
 * Custom Layout for a form (user-defined HTML).
 */
abstract class _Abstract extends \atk4\ui\View
{
    /**
     * Links layout to the form.
     *
     * @var \atk4\ui\Form
     */
    public $form;

    /**
     * Places field inside a layout somewhere. Should be called
     * through $form->addField().
     *
     * @param string                   $name
     * @param array|string|object|null $decorator
     * @param array|string|object|null $field
     *
     * @return \atk4\ui\FormField\Generic
     */
    public function addField($name, $decorator = null, $field = null)
    {
        if (!is_string($name)) {
            throw new Exception(['Format for addField now require first argument to be name']);
        }

        if (!$this->form->model) {
            $this->form->model = new \atk4\ui\misc\ProxyModel();
        }

        if (is_string($field)) {
            $field = ['type' => $field];
        }

        if ($name) {
            $existingField = $this->form->model->hasField($name);
        }

        try {
            if (!$existingField) {
                // Add missing field
                if ($field) {
                    $field = $this->form->model->addField($name, $field);
                } else {
                    $field = $this->form->model->addField($name);
                }
            } elseif (is_array($field)) {
                // Add properties to existing field
                $existingField->setDefaults($field);
                $field = $existingField;
            } elseif (is_object($field)) {
                throw new Exception(['Duplicate field', 'name' => $name]);
            } else {
                $field = $existingField;
            }

            if (is_string($decorator)) {
                $decorator = $this->form->decoratorFactory($field, ['caption' => $decorator]);
            } elseif (is_array($decorator)) {
                $decorator = $this->form->decoratorFactory($field, $decorator);
            } elseif (!$decorator) {
                $decorator = $this->form->decoratorFactory($field);
            } elseif (is_object($decorator)) {
                if (!$decorator instanceof \atk4\ui\FormField\Generic) {
                    throw new Exception(['Field decorator must descend from \atk4\ui\FormField\Generic', 'decorator' => $decorator]);
                }
                $decorator->field = $field;
                $decorator->form = $this->form;
            } else {
                throw new Exception(['Value of $decorator argument is incorrect', 'decorator' => $decorator]);
            }
        } catch (\Throwable $e) {
            throw new Exception(['Unable to add form field', 'name' => $name, 'decorator' => $decorator, 'field' => $field], 0, $e);
        }

        return $this->_addField($decorator, $field);
    }

    protected function _addField($decorator, $field)
    {
        return $this->add($decorator, $this->template->hasTag($field->short_name) ? $field->short_name : null);
    }

    /**
     * Add more than one field in one shot.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function addFields($fields)
    {
        foreach ($fields as $field) {
            $this->addField(...(array) $field);
        }

        return $this;
    }

    /**
     * Returns array of names of fields to automatically include them in form.
     * This includes all editable or visible fields of the model.
     *
     * @return array
     */
    protected function getModelFields(\atk4\data\Model $model)
    {
        return array_keys($model->getFields('editable'));
    }

    /**
     * Sets form model and adds form fields.
     *
     * @param array|null $fields
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $model, $fields = null)
    {
        parent::setModel($model);

        if ($fields === false) {
            return $model;
        }

        if ($fields === null) {
            $fields = $this->getModelFields($model);
        }

        // prepare array of fields - check if fields are editable or read_only/disabled
        $add_fields = [];
        foreach ($fields as $field) {
            $f = $model->getField($field);

            if ($f->isEditable()) {
                $add_fields[] = [$f->short_name];
            } elseif ($f->isVisible()) {
                $add_fields[] = [$f->short_name, ['readonly' => true]];
            }
        }

        if (is_array($add_fields)) {
            foreach ($add_fields as $field) {
                call_user_func_array([$this, 'addField'], $field);
            }
        } else {
            throw new Exception(['Incorrect value for $fields', 'fields' => $add_fields]);
        }

        return $model;
    }

    /**
     * Return Field decorator associated with
     * the form's field.
     *
     * @param string $name
     *
     * @return \atk4\ui\FormField\Generic
     */
    public function getField($name)
    {
        if (empty($this->form)) {
            throw new Exception(['Incorrect value for $form', 'form' => $this->form]);
        }

        return $this->form->getField($name);
    }

    /**
     * Adds Button into form layout.
     *
     * @param Button|array|string $seed
     *
     * @return \atk4\ui\Button
     */
    abstract public function addButton($seed);
}
