<?php

namespace atk4\ui\TableColumn;

use atk4\data\Field;
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
    /**
     * The form associate with this FilterPopup.
     *
     * @var Form
     */
    public $form;

    /**
     * The table field that need filtering.
     *
     * @var Field
     */
    public $field;

    /**
     * The view associate with this filter popup that need to be reload.
     *
     * @var View|null
     */
    public $reload;

    /**
     * The Table Column triggering the poupup.
     * This is need to simulate click in order to properly
     * close the popup window on "Clear".
     *
     * @var string
     */
    public $colTrigger;

    public function init(): void
    {
        parent::init();
        $this->setOption('delay', ['hide' => 1500]);
        $this->setHoverable();

        $m = Generic::factoryType($this->field);

        $this->form = Form::addTo($this)->addClass('');
        $this->form->buttonSave->addClass('');
        $this->form->addGroup("Where {$this->field->getCaption()} :");

        $this->form->buttonSave->set('Set');

        $this->form->setFieldsDisplayRules($m->getFormDisplayRules());

        //load first and only record associate with this popup.
        $this->form->setModel($m->tryLoadAny());

        $this->form->onSubmit(function ($f) {
            $f->model->save();
            //trigger click action in order to close popup.
            //otherwise calling ->popup('hide') is not working as expected.
            return (new jQuery($this->triggerBy))->trigger('click');
        });

        \atk4\ui\Button::addTo($this->form, ['Clear', 'clear '])->on('click', function ($f) use ($m) {
            $m->clearData();

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
     * Recall model data.
     *
     * @return mixed
     */
    public function recallData()
    {
        return $this->form->model->recallData();
    }

    /**
     * Set filter condition base on the field Type model use in this FilterPopup.
     *
     * @return mixed
     */
    public function setFilterCondition($tableModel)
    {
        return $this->form->model->setConditionForModel($tableModel);
    }
}
