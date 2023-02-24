<?php

declare(strict_types=1);

namespace Atk4\Ui\Form;

use Atk4\Core\WarnDynamicPropertyTrait;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;
use Atk4\Ui\View;

/**
 * Custom Layout for a form.
 */
abstract class AbstractLayout extends View
{
    use WarnDynamicPropertyTrait;

    /** Links layout to owner Form. */
    public Form $form;

    protected function _addControl(Control $control, Field $field): Control
    {
        return $this->add($control, $this->template->hasTag($field->shortName) ? $field->shortName : null);
    }

    /**
     * Places element inside a layout somewhere. Should be called
     * through $form->addControl().
     *
     * @param array<mixed>|Control $control
     * @param array<mixed>         $fieldSeed
     */
    public function addControl(string $name, $control = [], array $fieldSeed = []): Control
    {
        if ($this->form->model === null) {
            $this->form->model = (new \Atk4\Ui\Misc\ProxyModel())->createEntity();
        }
        $this->form->model->assertIsEntity();

        // TODO this class should not refer to any specific form control
        $controlClass = is_object($control)
            ? get_class($control)
            : ($control[0] ?? (($fieldSeed['ui'] ?? [])['form'][0] ?? null));
        if (is_a($controlClass, Control\Checkbox::class, true)) {
            $fieldSeed['type'] = 'boolean';
        } elseif (is_a($controlClass, Control\Calendar::class, true)) {
            $calendarType = $control instanceof Control\Calendar ? $control->type : ($control['type'] ?? null);
            if ($calendarType !== null) {
                $fieldSeed['type'] = $calendarType;
            }
        }

        try {
            if (!$this->form->model->hasField($name)) {
                $field = $this->form->model->getModel()->addField($name, $fieldSeed);
            } else {
                $field = $this->form->model->getField($name)
                    ->setDefaults($fieldSeed);
            }

            $control = $this->form->controlFactory($field, $control);
        } catch (\Exception $e) {
            throw (new Exception('Unable to create form control', 0, $e))
                ->addMoreInfo('name', $name)
                ->addMoreInfo('control', $control)
                ->addMoreInfo('field', $fieldSeed);
        }

        return $this->_addControl($control, $field);
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
    public function setModel(Model $entity, array $fields = null): void
    {
        $entity->assertIsEntity();

        parent::setModel($entity);

        if ($fields === null) {
            $fields = $this->getModelFields($entity);
        }

        // add controls - check if fields are editable or read-only/disabled
        foreach ($fields as $fieldName) {
            $field = $entity->getField($fieldName);

            $controlSeed = null;
            if ($field->isEditable()) {
                $controlSeed = [];
            } elseif ($field->isVisible()) {
                $controlSeed = ['readOnly' => true];
            }

            if ($controlSeed !== null) {
                $this->addControl($field->shortName, $controlSeed);
            }
        }
    }

    /**
     * Return Field decorator associated with the form's field.
     */
    public function getControl(string $name): Control
    {
        return $this->form->getControl($name);
    }

    /**
     * Adds Button into form layout.
     *
     * @param Button|array $seed
     *
     * @return Button
     */
    abstract public function addButton($seed);
}
