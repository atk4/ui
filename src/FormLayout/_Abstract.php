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
     */
    public $form = null;

    /**
     * Places field inside a layout somewhere. Should be called
     * through $form->addField().
     *
     * @param string|null              $name
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
            $existingField = $this->form->model->hasElement($name);
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
            throw new Exception(['Unable to add form field', 'name' => $name, 'decorator' => $decorator, 'field' => $field], null, $e);
        }

        return $this->_addField($decorator, $field);
    }

    protected function _addField($decorator, $field)
    {
        return $this->add($decorator, $this->template->hasTag($field->short_name) ? $field->short_name : null);
    }

    public function setModel(\atk4\data\Model $model, $fields = null)
    {
        parent::setModel($model);

        if ($fields === false) {
            return $model;
        }

        if ($fields === null) {
            $fields = [];
            foreach ($model->elements as $f) {
                if (!$f instanceof \atk4\data\Field) {
                    continue;
                }

                if (!$f->isEditable()) {
                    continue;
                }
                $fields[] = $f->short_name;
            }
        }

        if (is_array($fields)) {
            foreach ($fields as $field) {
                $this->addField($field);
            }
        } else {
            throw new Exception(['Incorrect value for $fields', 'fields' => $fields]);
        }

        return $model;
    }

    /**
     * Return Field decorator associated with
     * the form's field.
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
     * @param array|string $button
     *
     * @return \atk4\ui\Button
     */
    abstract public function addButton($button);
}
