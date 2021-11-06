<?php

declare(strict_types=1);

namespace Atk4\Ui\Form;

use Atk4\Core\WarnDynamicPropertyTrait;
use Atk4\Ui\Exception;

/**
 * Custom Layout for a form (user-defined HTML).
 */
abstract class AbstractLayout extends \Atk4\Ui\View
{
    use WarnDynamicPropertyTrait;

    /**
     * Links layout to the form.
     *
     * @var \Atk4\Ui\Form
     */
    public $form;

    /**
     * Places element inside a layout somewhere. Should be called
     * through $form->addControl().
     *
     * @param array|string|object|null $control
     * @param array|string|object|null $field
     *
     * @return \Atk4\Ui\Form\Control
     */
    public function addControl(string $name, $control = null, $field = null)
    {
        if ($this->form->model === null) {
            $this->form->model = (new \Atk4\Ui\Misc\ProxyModel())->createEntity();
        }

        if (is_string($field)) {
            $field = ['type' => $field];
        } elseif (is_array($control) && isset($control['type'])) {
            $field = ['type' => $control['type']];
        }

        try {
            if (!$this->form->model->hasField($name)) {
                $this->form->model->getModel()->addField($name, $field);
                $field = $this->form->model->addField($name, $field); // TODO adding field to a model MUST be enough
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
            } elseif ($control === null) {
                $control = $this->form->controlFactory($field);
            } elseif ($control instanceof Control) {
                $control = $this->form->controlFactory($field, $control);
            } else {
                throw (new Exception('Value of $control argument is incorrect'))
                    ->addMoreInfo('control', $control);
            }
        } catch (\Exception $e) {
            throw (new Exception('Unable to add form control', 0, $e))
                ->addMoreInfo('name', $name)
                ->addMoreInfo('control', $control)
                ->addMoreInfo('field', $field);
        }

        return $this->_addControl($control, $field);
    }

    protected function _addControl($decorator, $field)
    {
        return $this->add($decorator, $this->template->hasTag($field->short_name) ? $field->short_name : null);
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
    protected function getModelFields(\Atk4\Data\Model $model)
    {
        return array_keys($model->getFields('editable'));
    }

    /**
     * Sets form model and adds form controls.
     *
     * @param array<int, string>|null $fields
     *
     * @return \Atk4\Data\Model
     */
    public function setModel(\Atk4\Data\Model $model, array $fields = null)
    {
        $model->assertIsEntity();

        parent::setModel($model);

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
     * Return Field decorator associated with
     * the form's field.
     */
    public function getControl(string $name): Control
    {
        return $this->form->getControl($name);
    }

    /**
     * Adds Button into form layout.
     *
     * @param \Atk4\Ui\Button|array|string $seed
     *
     * @return \Atk4\Ui\Button
     */
    abstract public function addButton($seed);
}
