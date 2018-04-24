<?php

namespace atk4\ui\TableColumn\FilterModel;

use atk4\core\SessionTrait;
use atk4\data\Field;
use atk4\data\Model;
use atk4\data\Persistence;
use atk4\data\Persistence_Array;
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
     * Whether or not this filter model use session to store it's data.
     *
     * @var bool
     */
    public $useSession = false;

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
    public static function factoryType($field/*, $persistence*/)
    {
        $data = [];
        $persistence = new Persistence_Array($data);
        $filterDomain = 'atk4\\ui\\TableColumn\\FilterModel\Type';

        // check if field as a type and use string as default
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

        return new $class($persistence, ['lookupField' => $field, 'useSession' => true]);
    }

    public function init()
    {
        parent::init();
        $this->op = $this->addField('op', ['ui' => ['caption' => '']]);
        $this->value = $this->addField('value', ['ui' => ['caption' => '']]);
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

        if ($this->useSession) {
            // create a name for our filter model to save as session data.
            $this->name = 'filter_model_'.$this->lookupField->short_name;

            if (@$_GET['atk_clear_filter']) {
                $this->forget();
            }

            if ($data = $this->recallData()) {
                $this->persistence->data['data'][] = $data;
            }

            // Add hook in order to persist data in session.
            $this->addHook('afterSave', function($m) {
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
