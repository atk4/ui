<?php

namespace atk4\ui\TableColumn;

use atk4\core\SessionTrait;
use atk4\data\Field;
use atk4\data\Persistence_Array;
use atk4\ui\Form;
use atk4\ui\jsReload;
use \atk4\ui\TableColumn\FilterModel\Generic;
use atk4\ui\Popup;

/**
 * Implement a filterPopup in a table column.
 * The popup contains a form associate to a field type model
 * and use session to store it's data.
 */
class FilterPopup extends Popup
{
    /**
     * This view use session to store model data.
     */
    use SessionTrait;

    /**
     * The data associcate to this model.
     *
     * @var array
     */
    public $data = [];

    /**
     * The form associate with this FilterPopup.
     *
     * @var Form
     */
    public $form = null;

    /**
     * The table field that need filtering.
     *
     * @var Field
     */
    public $field = null;

    /**
     * The view associate with this filter popup that need to be reload.
     *
     * @var View|null
     */
    public $reload = null;

    public function init()
    {
        parent::init();

        if (@$_GET['atk_clear_filter']) {
            $this->forget();
        }

        // Get data back from session.
        $this->data = $this->recallData();

        $this->form = $this->add('Form')->addClass('mini');
        $this->form->buttonSave->addClass('tiny');
        $this->form->addGroup("Where {$this->field->short_name} :");

        $this->form->buttonSave->set('Set');

        //create filter data model according to field type.
        $m = Generic::factoryType(ucfirst($this->field->type), new Persistence_Array($this->data));
        $m ->addField('name', ['default'=> $this->field->short_name, 'system' => true]);

        //TODO Use When form condition is merge
        //$this->form->setFieldsDisplayRules($m->getFormDisplayRule());

        //load first and only record associate with this popup.
        $this->form->setModel($m->tryLoadAny());

        $this->form->onSubmit(function($f) {
            $f->model->save();
            $this->memorize($this->field->short_name, $this->data['data']);
        });
        $this->form->add(['Button', 'Clear', 'clear tiny'])->on('click', function($f) {
            $this->forget();
            return [$this->form->js()->form('reset'), new jsReload($this->reload)];
        });
    }

    public function isFilterOn()
    {
        $test = $this->recallData();
        return !empty($this->recallData());
    }

    /**
     * Recall session data.
     *
     * @return mixed
     */
    public function recallData()
    {
        return $this->recall($this->field->short_name);
    }

    /**
     * Set filter condition base on the field Type model use in this FilterPopup.
     *
     * @param $tableModel
     *
     * @return mixed
     */
    public function setFilterCondition($tableModel)
    {
        return $this->form->model->setConditionForModel($tableModel);
    }
}
