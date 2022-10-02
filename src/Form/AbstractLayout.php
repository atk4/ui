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
                $field = $this->form->model->getField($name)
                    ->setDefaults($field);
            }

            $control = $this->form->controlFactory($field, $control);
        } catch (\Exception $e) {
            throw (new Exception('Unable to create form control', 0, $e))
                ->addMoreInfo('name', $name)
                ->addMoreInfo('control', $control)
                ->addMoreInfo('field', $field);
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
    public function setModel(Model $model, array $fields = null): void
    {
        $model->assertIsEntity();

        parent::setModel($model);

        if ($fields === null) {
            $fields = $this->getModelFields($model);
        }

        // add controls - check if fields are editable or read-only/disabled
        foreach ($fields as $fieldName) {
            $field = $model->getField($fieldName);

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
