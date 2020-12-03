<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Ui\Form;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsReload;
use Atk4\Ui\Popup;

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
     * @var \Atk4\Ui\View|null
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

    protected function init(): void
    {
        parent::init();
        $this->setOption('delay', ['hide' => 1500]);
        $this->setHoverable();

        $model = FilterModel::factoryType($this->field);

        $this->form = Form::addTo($this)->addClass('');
        $this->form->buttonSave->addClass('');
        $this->form->addGroup("Where {$this->field->getCaption()} :");

        $this->form->buttonSave->set('Set');

        $this->form->setControlsDisplayRules($model->getFormDisplayRules());

        // load data associated with this popup.
        if ($data = $model->recallData()) {
            $model->setMulti($data);
        }
        $this->form->setModel($model);

        $this->form->onSubmit(function (Form $form) {
            $form->model->save();

            return new jsReload($this->reload);
        });

        \Atk4\Ui\Button::addTo($this->form, ['Clear', 'clear '])->on('click', function ($f) use ($model) {
            $model->clearData();

            return [
                $this->form->js(null, null, $this->form->formElement)->form('reset'),
                new JsReload($this->reload),
                (new Jquery($this->colTrigger))->trigger('click'),
            ];
        });
    }

    /**
     * Check if filter is on.
     */
    public function isFilterOn(): bool
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
