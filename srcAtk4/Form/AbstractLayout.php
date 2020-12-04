<?php

declare(strict_types=1);

namespace Atk4\Ui\Form;

use atk4\ui\Exception;

/**
 * Custom Layout for a form (user-defined HTML).
 */
abstract class AbstractLayout extends \atk4\ui\View
{
    /**
     * Links layout to the form.
     *
     * @var \atk4\ui\Form
     */
    public $form;

    /**
     * @deprecated use AbstractLayout::addControl instead - will be removed in dec-2020
     */
    public function addField(string $name, $decorator = null, $field = null)
    {
        'trigger_error'('Method is deprecated. Use AbstractLayout::addControl instead', E_USER_DEPRECATED);

        return $this->addControl(...func_get_args());
    }

    /**
     * Places element inside a layout somewhere. Should be called
     * through $form->addControl().
     *
     * @param array|string|object|null $control
     * @param array|string|object|null $field
     *
     * @return \atk4\ui\Form\Control
     */
    public function addControl(string $name, $control = null, $field = null)
    {
        if (!$this->form->model) {
            $this->form->model = new \atk4\ui\Misc\ProxyModel();
        }

        if (is_string($field)) {
            $field = ['type' => $field];
        }

        try {
            if (!$this->form->model->hasField($name)) {
                $field = $this->form->model->addField($name, $field);
            } else {
                $existingField = $this->form->model->getField($name);

                if (is_array($field)) {
                    $field = $existingField->setDefaults($field);
                } elseif (is_object($field)) {
                    throw (new Exception('Duplicate field'))
                        ->addMoreInfo('name', $name);
                } else {
                    $field = $existingField;
                }
            }

            if (is_string($control)) {
                $control = $this->form->controlFactory($field, ['caption' => $control]);
            } elseif (is_array($control)) {
                $control = $this->form->controlFactory($field, $control);
            } elseif (!$control) {
                $control = $this->form->controlFactory($field);
            } elseif (is_object($control)) {
                if (!$control instanceof \atk4\ui\Form\Control) {
                    throw (new Exception('Form control must descend from ' . \atk4\ui\Form\Control::class))
                        ->addMoreInfo('control', $control);
                }
                $control->field = $field;
                $control->form = $this->form;
            } else {
                throw (new Exception('Value of $control argument is incorrect'))
                    ->addMoreInfo('control', $control);
            }
        } catch (\Throwable $e) {
            throw (new Exception('Unable to add form control', 0, $e))
                ->addMoreInfo('name', $name)
                ->addMoreInfo('control', $control)
                ->addMoreInfo('field', $field);
        }

        if (method_exists($this, '_addField')) {
            // backward compatibility - will be removed in dec-2020
            'trigger_error'('Method _addField is deprecated. Override _addControl method instead', E_USER_DEPRECATED);

            return $this->_addField($control, $field);
        }

        return $this->_addControl($control, $field);
    }

    protected function _addControl($decorator, $field)
    {
        return $this->add($decorator, $this->template->hasTag($field->short_name) ? $field->short_name : null);
    }

    /**
     * @deprecated use AbstractLayout::addControls instead - will be removed in dec-2020
     */
    public function addFields($fields)
    {
        'trigger_error'('Method is deprecated. Use AbstractLayout::addControls instead', E_USER_DEPRECATED);

        return $this->addControls(...func_get_args());
    }

    /**
     * Add more than one control in one shot.
     *
     * @param array $controls
     *
     * @return $this
     */
    public function addControls($controls)
    {
        foreach ($controls as $control) {
            $this->addControl(...(array) $control);
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
     * Sets form model and adds form controls.
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

        // prepare array of controls - check if fields are editable or read_only/disabled
        $controls = [];
        foreach ($fields as $fieldName) {
            $field = $model->getField($fieldName);

            if ($field->isEditable()) {
                $controls[] = [$field->short_name];
            } elseif ($field->isVisible()) {
                $controls[] = [$field->short_name, ['readonly' => true]];
            }
        }

        if (is_array($controls)) {
            $this->addControls($controls);
        } else {
            throw (new Exception('Incorrect value for $fields'))
                ->addMoreInfo('controls', $controls);
        }

        return $model;
    }

    /**
     * @deprecated use AbstractLayout::getControl instead - will be removed in dec-2020
     */
    public function getField($name)
    {
        'trigger_error'('Method is deprecated. Use AbstractLayout::getControl instead', E_USER_DEPRECATED);

        return $this->getControl(...func_get_args());
    }

    /**
     * Return Field decorator associated with
     * the form's field.
     *
     * @return \atk4\ui\Form\Control
     */
    public function getControl(string $name): Control
    {
        if (empty($this->form)) {
            throw (new Exception('Incorrect value for $form'))
                ->addMoreInfo('form', $this->form);
        }

        return $this->form->getControl($name);
    }

    /**
     * Adds Button into form layout.
     *
     * @param \atk4\ui\Button|array|string $seed
     *
     * @return \atk4\ui\Button
     */
    abstract public function addButton($seed);
}
