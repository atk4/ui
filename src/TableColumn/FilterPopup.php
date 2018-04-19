<?php

namespace atk4\ui\TableColumn;

use atk4\core\SessionTrait;
use atk4\data\Field;
use atk4\data\Persistence_Array;
use atk4\ui\Form;
use atk4\ui\jQuery;
use atk4\ui\jsReload;
use atk4\ui\Popup;
use atk4\ui\TableColumn\FilterModel\Generic;

/**
 * Implement a filterPopup in a table column.
 * The popup contains a form associate to a field type model
 * and use session to store it's data.
 */
class FilterPopup extends Popup
{
    /*
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

    /**
     * The Table Column triggering the poupup.
     * This is need to simulate click in order to properly
     * close the popup window on "Clear".
     *
     * @var string
     */
    public $colTrigger;

    public function init()
    {
        parent::init();

        if (@$_GET['atk_clear_filter']) {
            $this->forget();
        }

        $this->setOption('delay', ['hide' => 1500]);
        $this->setHoverable();

        // Get data back from session.
        $this->data = $this->recallData();

        $this->form = $this->add('Form')->addClass('');
        $this->form->buttonSave->addClass('');
        $this->form->addGroup("Where {$this->field->getCaption()} :");

        $this->form->buttonSave->set('Set');

        //create filter data model according to field type.
        $m = Generic::factoryType(ucfirst($this->field->type), new Persistence_Array($this->data));
        $m->addField('name', ['default'=> $this->field->short_name, 'system' => true]);

        //TODO Use When form condition is merge
        //$this->form->setFieldsDisplayRules($m->getFormDisplayRule());

        //load first and only record associate with this popup.
        $this->form->setModel($m->tryLoadAny());

        $this->form->onSubmit(function ($f) {
            $f->model->save();
            $this->memorize($this->field->short_name, $this->data['data']);
            //trigger click action in order to close popup.
            //otherwise calling ->popup('hide') is not working as expected.
            return (new jQuery($this->triggerBy))->trigger('click');
        });
        $this->form->add(['Button', 'Clear', 'clear '])->on('click', function ($f) {
            $this->forget();

            return [
                $this->form->js()->form('reset'),
                new jsReload($this->reload),
                (new jQuery($this->colTrigger))->trigger('click'),
            ];
        });
    }

    /**
     * Check if filter is on.
     *
     * @return bool
     */
    public function isFilterOn()
    {
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
