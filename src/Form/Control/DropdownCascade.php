<?php

declare(strict_types=1);
/**
 * Dropdown form control that will based it's list value
 * according to another input value.
 * Also possible to cascade value from another cascade field.
 * For example:
 *  - you need to narrow product base on Category and sub category
 *       $form = Form::addTo($app);
 *       $form->addControl('category_id', [Dropdown::class, 'model' => new Category($db)])->set(3);
 *       $form->addControl('sub_category_id', [DropdownCascade::class, 'cascadeFrom' => 'category_id', 'reference' => 'SubCategories']);
 *       $form->addControl('product_id', [DropdownCascade::class, 'cascadeFrom' => 'sub_category_id', 'reference' => 'Products']);.
 */

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Form;

class DropdownCascade extends Dropdown
{
    /** @var string|Form\Control|null the form input to use for setting this dropdown list values from. */
    public $cascadeFrom;

    /** @var string|Model|null the hasMany reference model that will generated value for this dropdown list. */
    public $reference;

    /** @var Form\Control The form control object created based on cascadeFrom */
    protected $cascadeControl;

    /** @var string The casacade input value. */
    protected $cascadeControlValue;

    protected function init(): void
    {
        parent::init();

        if (!$this->cascadeFrom) {
            throw new Exception('cascadeFrom property is not set.');
        }

        $this->cascadeControl = is_string($this->cascadeFrom) ? $this->form->getControl($this->cascadeFrom) : $this->cascadeFrom;

        if (!$this->cascadeControl instanceof Form\Control) {
            throw new Exception('cascadeFrom property should be an instance of ' . Form\Control::class);
        }

        $this->cascadeControlValue = $_POST[$this->cascadeControl->name] ?? $this->cascadeControl->field->get('value');

        $this->model = $this->cascadeControl->model ? $this->cascadeControl->model->ref($this->reference) : null;

        // setup initial values and add it via dropdownOptions.
        $values = $this->getJsValues($this->getNewValues((string) $this->cascadeControlValue), (string) $this->field->get());
        $this->dropdownOptions = array_merge($this->dropdownOptions, ['values' => $values]);

        // js to execute for the onChange handler of the parent dropdown.
        $expr = [
            function ($t) {
                return [
                    $this->js()->dropdown('change values', $this->getNewValues((string) $this->cascadeControlValue)),
                    $this->js()->removeClass('loading'),
                ];
            },
            $this->js()->dropdown('clear'),
            $this->js()->addClass('loading'),
        ];

        $this->cascadeControl->onChange($expr, ['args' => [$this->cascadeControl->name => $this->cascadeControl->jsInput()->val()]]);
    }

    /**
     * Generate new dropdown values based on cascadeInput model selected id.
     * Return an empty value set if id is null.
     */
    public function getNewValues(string $id): array
    {
        if (!$id) {
            return [['value' => '', 'text' => $this->empty, 'name' => $this->empty]];
        }

        $model = $this->cascadeControl->model->load($id)->ref($this->reference);
        $values = [];
        foreach ($model as $k => $row) {
            if ($this->renderRowFunction) {
                $res = ($this->renderRowFunction)($row, $k);
                $values[] = ['value' => $res['value'], 'text' => $row->get('name'), 'name' => $res['title']];
            } else {
                $values[] = ['value' => $row->get($model->id_field), 'text' => $row->get($model->title_field), 'name' => $row->get($model->title_field)];
            }
        }

        return $values;
    }

    /**
     *  Will mark current value as selected from a list
     *  of possible values.
     *
     * @param array  $values an array of possible values
     * @param string $value  the current field value
     */
    private function getJsValues(array $values, string $value): array
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
