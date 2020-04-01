<?php

namespace atk4\ui\TableColumn\FilterModel;

use atk4\core\SessionTrait;
use atk4\data\Field;
use atk4\data\Model;
use atk4\data\Persistence;

/**
 * Implement a generic Type model for filtering data.
 */
class Generic extends Model
{
    use SessionTrait;

    /**
     * The operator for defining a condition on a field.
     *
     * @var
     */
    public $op;

    /**
     * The value for defining a condition on a field.
     *
     * @var
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
     * @var null
     */
    public $lookupField = null;

    /**
     * Factory method that will return a FilerModel Type class.
     *
     * @param Field       $field
     * @param Persistence $persistence
     *
     * @return mixed
     */
    public static function factoryType($field)
    {
        $data = [];
        $persistence = new Persistence\Array_($data);
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
        if (!empty($field->filterModel) && isset($field->filterModel)) {
            if ($field->filterModel instanceof Model) {
                return $field->filterModel;
            }
            $class = $field->filterModel;
        }

        return new $class($persistence, ['lookupField' => $field]);
    }

    public function init()
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
        $this->addField('name', ['default'=> $this->lookupField->short_name, 'system' => true]);

        if (isset($this->_sessionTrait) && $this->_sessionTrait) {
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
    }

    /**
     * Recall filter model data.
     *
     * @return array
     */
    public function recallData()
    {
        return $this->recall('data');
    }

    /**
     * Method that will set conditions on a model base on $op and $value value.
     * Each FilterModel\TypeModel should override this method.
     *
     * @return mixed
     */
    public function setConditionForModel($model)
    {
        return $model;
    }

    /**
     * Method that will set Field display condition in a form.
     * If form filter need to have a field display at certain condition, then
     * override this method in your FilterModel\TypeModel.
     *
     * @return null
     */
    public function getFormDisplayRules()
    {
    }

    /**
     * Check if this model is using session or not.
     *
     * @return bool
     */
    public function clearData()
    {
        $this->forget();
    }
}
