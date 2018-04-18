<?php

namespace atk4\ui\TableColumn\FilterModel;

use atk4\data\Model;

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

    /**
     * Factory method that will return a FilerModel Type class.
     *
     * @param $type
     * @param $persistence
     *
     * @return mixed
     */
    public static function factoryType($type, $persistence) {
        $class = 'atk4\\ui\\TableColumn\\FilterModel\Type'.$type;
        return new $class($persistence);
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
     * Method the will set Field display condition in a form.
     * If form filter need to have a field display at certain condition, then
     * override this method in your FilterModel\TypeModel.
     *
     * @return null
     */
    public function getFormDisplayRule()
    {
        return null;
    }
}
