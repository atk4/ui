<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Model;
use Atk4\Ui\Form;
use Atk4\Ui\Jquery;

/**
 * Dropdown form control that will based it's list value
 * according to another input value.
 * Also possible to cascade value from another cascade field.
 */
class DropdownCascade extends Dropdown
{
    /** @var string|Form\Control The form control to use for setting this dropdown list values from. */
    public $cascadeFrom;

    /** @var string|Model|null The hasMany reference model that will generate value for this dropdown list. */
    public $reference;

    protected function init(): void
    {
        parent::init();

        if (!$this->cascadeFrom instanceof Form\Control) {
            $this->cascadeFrom = $this->form->getControl($this->cascadeFrom);
        }

        $cascadeFromValue = isset($_POST[$this->cascadeFrom->name])
            ? $this->getApp()->uiPersistence->typecastLoadField($this->cascadeFrom->entityField->getField(), $_POST[$this->cascadeFrom->name])
            : $this->cascadeFrom->entityField->get();

        $this->model = $this->cascadeFrom->model ? $this->cascadeFrom->model->ref($this->reference) : null;

        // populate default dropdown values
        $this->dropdownOptions['values'] = $this->getJsValues($this->getNewValues($cascadeFromValue), $this->entityField->get());

        // js to execute for the onChange handler of the parent dropdown.
        $expr = [
            function (Jquery $j) use ($cascadeFromValue) {
                return [
                    $this->js()->dropdown('change values', $this->getNewValues($cascadeFromValue)),
                    $this->js()->removeClass('loading'),
                ];
            },
            $this->js()->dropdown('clear'),
            $this->js()->addClass('loading'),
        ];

        $this->cascadeFrom->onChange($expr, ['args' => [$this->cascadeFrom->name => $this->cascadeFrom->jsInput()->val()]]);
    }

    /**
     * Allow initializing CascadeDropdown with preset value.
     *
     * @param mixed $value The initial ID value to set this dropdown using reference model values
     * @param mixed $junk
     *
     * @return $this
     */
    public function set($value = null, $junk = null)
    {
        $this->dropdownOptions['values'] = $this->getJsValues($this->getNewValues($this->cascadeFrom->entityField->get()), $value);

        return parent::set($value, $junk);
    }

    /**
     * Generate new dropdown values based on cascadeInput model selected id.
     * Return an empty value set if id is null.
     *
     * @param string|int $id
     */
    public function getNewValues($id): array
    {
        if (!$id) {
            return [['value' => '', 'text' => $this->empty, 'name' => $this->empty]];
        }

        $model = $this->cascadeFrom->model->load($id)->ref($this->reference);
        $values = [];
        foreach ($model as $k => $row) {
            if ($this->renderRowFunction) {
                $res = ($this->renderRowFunction)($row, $k);
                $values[] = ['value' => $res['value'], 'text' => $row->get('name'), 'name' => $res['title']];
            } else {
                $values[] = ['value' => $row->getId(), 'text' => $row->get($model->titleField), 'name' => $row->get($model->titleField)];
            }
        }

        return $values;
    }

    /**
     * Will mark current value as selected from a list
     * of possible values.
     *
     * @param string|int $value the current field value
     */
    private function getJsValues(array $values, $value): array
    {
        foreach ($values as $k => $v) {
            if ($v['value'] === $value) {
                $values[$k]['selected'] = true;

                break;
            }
        }

        return $values;
    }

    protected function htmlRenderValue(): void
    {
        // Called in parent::renderView(), but values are rendered only via js
    }

    protected function renderView(): void
    {
        // multiple selection is not supported
        $this->isMultiple = false;

        parent::renderView();
    }
}
