<?php
/**
 * Dropdown form field that will based it's list value
 * according to another input value.
 * Also possible to cascade value from another cascade field.
 * For example:
 *  - you need to narrow product base on Category and sub category
 *       $f = Form::addTo($app);
 *       $f->addField('category_id', [DropDown::class, 'model' => new Category($db)])->set(3);
 *       $f->addField('sub_category_id', [DropDownCascade::class, 'cascadeFrom' => 'category_id', 'reference' => 'SubCategories']);
 *       $f->addField('product_id', [DropDownCascade::class, 'cascadeFrom' => 'sub_category_id', 'reference' => 'Products']);.
 */

namespace atk4\ui\FormField;

use atk4\data\Model;
use atk4\ui\Exception;

class DropDownCascade extends DropDown
{
    /** @var string|Generic|null the form input to use for setting this dropdown list values from. */
    public $cascadeFrom;

    /** @var string|Model|null the hasMany reference model that will generated value for this dropdown list. */
    public $reference;

    /** @var null The form input create by cascadeFrom field */
    protected $cascadeInput;

    /** @var null The casacade input value. */
    protected $cascadeInputValue;

    public function init(): void
    {
        parent::init();

        if (!$this->cascadeFrom) {
            throw new Exception('cascadeFrom property is not set.');
        }

        $this->cascadeInput = is_string($this->cascadeFrom) ? $this->form->getField($this->cascadeFrom) : $this->cascadeFrom;

        if (!$this->cascadeInput instanceof Generic) {
            throw new Exception('cascadeFrom property should be an instance of ' . Generic::class);
        }

        $this->cascadeInputValue = $_POST[$this->cascadeInput->name] ?? $this->cascadeInput->field->get('value');

        $this->model = $this->cascadeInput->model ? $this->cascadeInput->model->ref($this->reference) : null;
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

        $model = $this->cascadeInput->model->load($id)->ref($this->reference);
        $values = [];
        foreach ($model as $k => $row) {
            if ($this->renderRowFunction && is_callable($this->renderRowFunction)) {
                $res = call_user_func($this->renderRowFunction, $row, $k);
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
     * @param $values    an array of possible values
     * @param $value     the current field value
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
     * Call during parent::renderView().
     *
     * @throws \atk4\core\Exception
     */
    protected function jsRenderDropdown(): array
    {
        // setup initial values.
        $values = $this->getJsValues($this->getNewValues((string) $this->cascadeInputValue), (string) $this->field->get());
        $options = array_merge($this->dropdownOptions, ['values' => $values]);

        // setup on change handler
        $expr = [
            function ($t) {
                return [
                    $this->js()->dropdown('change values', $this->getNewValues((string) $this->cascadeInputValue)),
                    $this->js()->removeClass('loading'),
                ];
            },
            $this->js()->dropdown('clear'),
            $this->js()->addClass('loading'),
        ];

        return [
            $this->js(true)->dropdown($options),
            $this->cascadeInput->onChange($expr, ['args' => [$this->cascadeInput->name => $this->cascadeInput->jsInput()->val()]]),
        ];
    }

    /**
     * Call during parent::renderView()
     * Cascade Dropdown values are only render via js.
     */
    protected function htmlRenderValue()
    {
    }

    public function renderView()
    {
        // can't be multiple selection.
        $this->isMultiple = false;
        parent::renderView();
    }
}
