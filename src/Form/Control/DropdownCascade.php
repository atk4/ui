<?php

declare(strict_types=1);
/**
 * Dropdown form control that will based it's list value
 * according to another input value.
 * Also possible to cascade value from another cascade field.
 */

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;

class DropdownCascade extends Dropdown
{
    /** @var string|Form\Control|null the form input to use for setting this dropdown list values from. */
    public $cascadeFrom;

    /** @var string|Model|null the hasMany reference model that will generate value for this dropdown list. */
    public $reference;

    /** @var Form\Control The form control object created based on cascadeFrom */
    protected $cascadeFromControl;

    /** @var string|int The cascade input value. */
    protected $cascadeFromControlValue;

    protected function init(): void
    {
        parent::init();

        if (!$this->cascadeFrom) {
            throw new Exception('cascadeFrom property is not set.');
        }

        $this->cascadeFromControl = is_string($this->cascadeFrom) ? $this->form->getControl($this->cascadeFrom) : $this->cascadeFrom;

        if (!$this->cascadeFromControl instanceof Form\Control) {
            throw new Exception('cascadeFrom property should be an instance of ' . Form\Control::class);
        }

        $this->cascadeFromControlValue = $_POST[$this->cascadeFromControl->name] ?? $this->cascadeFromControl->field->get();

        $this->model = $this->cascadeFromControl->model ? $this->cascadeFromControl->model->ref($this->reference) : null;

        // populate default dropdown values
        $this->dropdownOptions['values'] = $this->getJsValues($this->getNewValues($this->cascadeFromControlValue), $this->field->get());

        // js to execute for the onChange handler of the parent dropdown.
        $expr = [
            function ($t) {
                return [
                    $this->js()->dropdown('change values', $this->getNewValues($this->cascadeFromControlValue)),
                    $this->js()->removeClass('loading'),
                ];
            },
            $this->js()->dropdown('clear'),
            $this->js()->addClass('loading'),
        ];

        $this->cascadeFromControl->onChange($expr, ['args' => [$this->cascadeFromControl->name => $this->cascadeFromControl->jsInput()->val()]]);
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
        $this->dropdownOptions['values'] = $this->getJsValues($this->getNewValues($this->cascadeFromControl->field->get()), $value);

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

        $model = $this->cascadeFromControl->model->tryLoad($id)->ref($this->reference);
        $values = [];
        foreach ($model as $k => $row) {
            if ($this->renderRowFunction) {
                $res = ($this->renderRowFunction)($row, $k);
                $values[] = ['value' => $res['value'], 'text' => $row->get('name'), 'name' => $res['title']];
            } else {
                $values[] = ['value' => $row->getId(), 'text' => $row->get($model->title_field), 'name' => $row->get($model->title_field)];
            }
        }

        return $values;
    }

    /**
     *  Will mark current value as selected from a list
     *  of possible values.
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

    /**
     * Call during parent::renderView()
     * Cascade Dropdown values are only render via js.
     */
    protected function htmlRenderValue()
    {
    }

    protected function renderView(): void
    {
        // can't be multiple selection.
        $this->isMultiple = false;
        parent::renderView();
    }
}
