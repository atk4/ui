<?php

namespace atk4\ui\TableColumn\FilterModel;

//use atk4\core\SessionTrait;
use atk4\data\Field;
use atk4\data\Model;
use atk4\data\Persistence;

/**
 * Implement a generic Type model for filtering data.
 */
class Generic extends Model
{
    //use SessionTrait;

    /**
     * The operator for defining a condition on a field.
     *
     * @var Field
     */
    public $op;

    /**
     * The value for defining a condition on a field.
     *
     * @var Field
     */
    public $value;

    /**
     * Determines if this field shouldn't have a value field, and use only op field.
     *
     * @var bool
     */
    public $noValueField = false;

    /**
     * The field where this filter need to query data.
     *
     * @var Field
     */
    public $lookupField;

    /**
     * Factory method that will return a FilerModel Type class.
     *
     * @return FilterModel
     */
    public static function factoryType(Field $field)
    {
        $persistence = new Persistence\Session();
        $filterDomain = 'atk4\\ui\\TableColumn\\FilterModel\Type';

        // check if field as a type and use string as default
        if (empty($type = $field->type)) {
            $type = 'string';
        }
        $class = $filterDomain . ucfirst($type);

        /*
         * You can set your own filter model condition by extending
         * Field class and setting your filter model class.
         */
        if (!empty($field->filterModel)) {
            if ($field->filterModel instanceof Model) {
                return $field->filterModel;
            }
            $class = $field->filterModel;
        }

        return new $class($persistence, ['lookupField' => $field]);
    }

    public function init(): void
    {
        parent::init();
        $this->op = $this->addField('op', ['ui' => ['caption' => '']]);

        if (!$this->noValueField) {
            $this->value = $this->addField('value', ['ui' => ['caption' => '']]);
        }

        $this->afterInit();
    }

    /**
     * Perform further initialisation.
     *
     * @throws \atk4\core\Exception
     */
    public function afterInit()
    {
        $this->addField('name', ['default' => $this->lookupField->short_name, 'system' => true]);

        // create a name for our filter model to save as session data.
        $this->table = 'filter_model_' . $this->lookupField->short_name;

        // delete stored filter data
        if ($_GET['atk_clear_filter'] ?? false) {
            $this->clearData();
        }

        /*
        if (isset($this->_sessionTrait)) {
            // create a name for our filter model to save as session data.
            $this->name = 'filter_model_' . $this->lookupField->short_name;

            if ($_GET['atk_clear_filter'] ?? false) {
                $this->forget();
            }

            if ($data = $this->recallData()) {
                $this->persistence->data['data'][] = $data;
            }

            // Add hook in order to persist data in session.
            $this->onHook('afterSave', function ($m) {
                $this->memorize('data', $m->get());
            });
        }
        */
    }

    /**
     * Recall filter model data.
     *
     * @return array
     */
    /*
    public function recallData()
    {
        return $this->recall('data');
    }
    */

    /**
     * Clears all stored data of this model.
     */
    public function clearData()
    {
        foreach ($this as $junk) {
            $this->delete();
        }
    }

    /**
     * Method that will set conditions on a model based on $op and $value value.
     * Each FilterModel\TypeModel should override this method.
     */
    public function setConditionForModel(Model $model): Model
    {
        return $model;
    }

    /**
     * Method that will set Field display condition in a form.
     * If form filter need to have a field display at certain condition, then
     * override this method in your FilterModel\TypeModel.
     *
     * @return array
     */
    public function getFormDisplayRules()
    {
    }
}
