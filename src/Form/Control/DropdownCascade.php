<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Model;
use Atk4\Ui\Form;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;

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

    #[\Override]
    protected function init(): void
    {
        parent::init();

        if (!$this->cascadeFrom instanceof Form\Control) {
            $this->cascadeFrom = $this->form->getControl($this->cascadeFrom);
        }

        $cascadeFromValue = $this->getApp()->hasRequestPostParam($this->cascadeFrom->name)
            ? $this->getApp()->uiPersistence->typecastAttributeLoadField($this->cascadeFrom->entityField->getField(), $this->getApp()->getRequestPostParam($this->cascadeFrom->name))
            : $this->cascadeFrom->entityField->get();

        $this->model = $this->cascadeFrom->model ? $this->cascadeFrom->model->ref($this->reference) : null;

        // populate default dropdown values
        $this->dropdownOptions['values'] = $this->getJsValues($this->getNewValues($cascadeFromValue), $this->entityField->get());

        // JS to execute for the onChange handler of the parent dropdown
        $expr = [
            function (Jquery $j) use ($cascadeFromValue) {
                return new JsBlock([
                    $this->jsDropdown()->dropdown('change values', $this->getNewValues($cascadeFromValue)),
                    $this->jsDropdown()->removeClass('loading'),
                ]);
            },
            $this->jsDropdown()->dropdown('clear'),
            $this->jsDropdown()->addClass('loading'),
        ];

        $this->cascadeFrom->onChange($expr, ['args' => [$this->cascadeFrom->name => $this->cascadeFrom->jsInput()->val()]]);
    }

    #[\Override]
    public function set($value = null)
    {
        $this->dropdownOptions['values'] = $this->getJsValues($this->getNewValues($this->cascadeFrom->entityField->get()), $value);

        return parent::set($value);
    }

    /**
     * Generate new dropdown values based on cascadeInput model selected ID.
     * Return an empty value set if ID is null.
     *
     * @param mixed $id
     *
     * @return list<array{value: string, text: mixed, name: mixed}>
     */
    public function getNewValues($id): array
    {
        if ($id === null) {
            return [[
                'value' => '',
                'text' => $this->empty,
                'name' => $this->empty,
            ]];
        }

        $model = $this->cascadeFrom->model->load($id)->ref($this->reference);
        $values = [];
        foreach ($model as $row) {
            if ($this->renderRowFunction) {
                $res = ($this->renderRowFunction)($row);
                $values[] = [
                    'value' => $this->getApp()->uiPersistence->typecastAttributeSaveField($model->getIdField(), $row->getId()),
                    'text' => $res['title'],
                    'name' => $res['title'],
                ];
            } else {
                $values[] = [
                    'value' => $this->getApp()->uiPersistence->typecastAttributeSaveField($model->getIdField(), $row->getId()),
                    'text' => $row->get($model->titleField),
                    'name' => $row->get($model->titleField),
                ];
            }
        }

        return $values;
    }

    /**
     * Will mark current value as selected from a list
     * of possible values.
     *
     * @param list<array{value: string, text: mixed, name: mixed}> $values
     * @param mixed                                                $value  the current field value
     */
    private function getJsValues(array $values, $value): array
    {
        $model = $this->cascadeFrom->model->ref($this->reference);
        $valueStr = $this->getApp()->uiPersistence->typecastAttributeSaveField($model->getIdField(), $value);

        foreach ($values as $k => $v) {
            if ($v['value'] === $valueStr) {
                $values[$k]['selected'] = true;

                break;
            }
        }

        return $values;
    }

    #[\Override]
    protected function htmlRenderValue(): void
    {
        // called in parent::renderView(), but values are rendered only via JS
    }

    #[\Override]
    protected function renderView(): void
    {
        // multiple selection is not supported
        $this->multiple = false;

        parent::renderView();
    }
}
