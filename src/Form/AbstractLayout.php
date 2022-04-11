<?php

declare(strict_types=1);

namespace Atk4\Ui\Form;

use Atk4\Core\WarnDynamicPropertyTrait;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Exception;

/**
 * Custom Layout for a form (user-defined HTML).
 */
abstract class AbstractLayout extends \Atk4\Ui\View
{
    use WarnDynamicPropertyTrait;

    /** @var \Atk4\Ui\Form Links layout to the form. */
    public $form;

    /**
     * Places element inside a layout somewhere. Should be called
     * through $form->addControl().
     *
     * @param array|Control $control
     * @param array|Field   $field
     */
    public function addControl(string $name, $control = [], $field = []): Control
    {
        if ($this->form->model === null) {
            $this->form->model = (new \Atk4\Ui\Misc\ProxyModel())->createEntity();
        }
        $this->form->model->assertIsEntity();

        if (is_array($control) && isset($control['type'])) {
            $field['type'] = $control['type'];
        }

        try {
            if (!$this->form->model->hasField($name)) {
                $field = $this->form->model->getModel()->addField($name, $field);
            } else {
                $existingField = $this->form->model->getField($name);

                if (is_array($field)) {
                    $field = $existingField->setDefaults($field);
                } else {
                    throw (new Exception('Duplicate field'))
                        ->addMoreInfo('name', $name);
                }
            }

            $control = $this->form->controlFactory($field, $control);
        } catch (\Exception $e) {
            throw (new Exception('Unable to add form control', 0, $e))
                ->addMoreInfo('name', $name)
                ->addMoreInfo('control', $control)
                ->addMoreInfo('field', $field);
        }

        return $this->_addControl($control, $field);
    }

    protected function _addControl(Control $control, Field $field): Control
    {
        return $this->add($control, $this->template->hasTag($field->shortName) ? $field->shortName : null);
    }

    /**
     * @param array<int, array> $controls
     *
     * @return $this
     */
    public function addControls(array $controls)
    {
        foreach ($controls as $control) {
            $this->addControl(...$control);
        }

        return $this;
    }

    /**
     * Returns array of names of fields to automatically include them in form.
     * This includes all editable or visible fields of the model.
     *
     * @return array
     */
    protected function getModelFields(Model $model)
    {
        return array_keys($model->getFields('editable'));
    }

    /**
     * Sets form model and adds form controls.
     *
     * @param array<int, string>|null $fields
     */
    public function setModel(Model $model, array $fields = null): void
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
                $controls[] = [$field->shortName];
            } elseif ($field->isVisible()) {
                $controls[] = [$field->shortName, ['readonly' => true]];
            }
        }

        if (is_array($controls)) {
            $this->addControls($controls);
        } else {
            throw (new Exception('Incorrect value for $fields'))
                ->addMoreInfo('controls', $controls);
        }
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
