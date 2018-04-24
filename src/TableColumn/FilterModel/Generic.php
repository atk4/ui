<?php

namespace atk4\ui\TableColumn\FilterModel;

use atk4\data\Field;
use atk4\data\Model;
use atk4\data\Persistence;

/**
 * Implement a generic Type model for filtering data.
 */
class Generic extends Model
{
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

    public $lookupField = null;

    /**
     * Factory method that will return a FilerModel Type class.
     *
     * @param Field       $field
     * @param Persistence $persistence
     *
     * @return mixed
     */
    public static function factoryType($field, $persistence)
    {
        $filterDomain = 'atk4\\ui\\TableColumn\\FilterModel\Type';

        // check if field as a type
        if (empty($type = $field->type)) {
            $type = 'string';
        }
        $class = $filterDomain.ucfirst($type);

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
        //$this->addField('op', [new \atk4\ui\DropDown()]);
        $this->op = $this->addField('op', ['ui' => ['caption' => '']]);
        $this->value = $this->addField('value', ['ui' => ['caption' => '']]);
    }

    /**
     * Method that will set conditions on a model base on $op and $value value.
     * Each FilterModel\TypeModel should override this method.
     *
     * @param $model
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
    public function getFormDisplayRule()
    {
    }
}
