<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

use atk4\data\UserAction\Action;
use atk4\data\UserAction\Generic;
use atk4\ui\ActionExecutor\jsUserAction;
use atk4\ui\ActionExecutor\UserAction;

/**
 * Implements a more sophisticated and interractive Data-Table component.
 */
class CRUD extends Grid
{
    /** @var array of fields to display in Grid */
    public $displayFields = null;

    /** @var array of fields to edit in Form */
    public $editFields = null;

    /** @var array Default action to perform when adding or editing is successful * */
    public $notifyDefault = ['jsToast', 'settings'=> ['message' => 'Data is saved!', 'class' => 'success']];

    /** @var string default js action executor class in UI for model action. */
    public $jsExecutor = jsUserAction::class;

    /** @var string default action executor class in UI for model action. */
    public $executor = UserAction::class;

    /**
     * Sets data model of CRUD.
     *
     * @param \atk4\data\Model $m
     * @param array            $fields
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $m, $fields = null)
    {
        if ($fields !== null) {
            $this->displayFields = $fields;
        }

        parent::setModel($m, $this->displayFields);

        $this->model->unload();

        foreach ($m->getActions(Generic::SINGLE_RECORD) as $single_record_action) {
            $executor = $this->factory($this->getActionExecutor($single_record_action));
            $single_record_action->fields = ($executor instanceof jsUserAction) ? false : $this->editFields ?? true;
            $single_record_action->ui['executor'] = $executor;
            $executor->addHook('afterExecute', function ($x, $m, $id) {
                if ($m->loaded()) {
                    $js = $this->jsSave($this->notifyDefault);
                } else {
                    $js = $this->jsDelete();
                }

                return $js;
            });
            $this->addAction($single_record_action);
        }

        foreach ($m->getActions(Generic::NO_RECORDS) as $single_record_action) {
            $executor = $this->factory($this->getActionExecutor($single_record_action));
            $single_record_action->fields = ($executor instanceof jsUserAction) ? false : $this->editFields ?? true;
            $single_record_action->ui['executor'] = $executor;
            $executor->addHook('afterExecute', function ($x, $m, $id) {
                if ($m->loaded()) {
                    $js = $this->jsSave($this->notifyDefault);
                }

                return $js;
            });
            $btn = $this->menu->addItem(['Add new '.$this->model->getModelCaption(), 'icon' => 'plus']);
            $btn->on('click.atk_CRUD', $single_record_action, [$this->name.'_sort' => $this->getSortBy()]);
        }

        return $this->model;
    }

    /**
     * Return proper action executor base on model action.
     *
     * @param $action
     *
     * @return object
     * @throws \atk4\core\Exception
     */
    protected function getActionExecutor($action)
    {
        $executor = (!$action->args && !$action->fields && !$action->preview) ? $this->jsExecutor : $this->executor;

        return $this->factory($executor);
    }

    /**
     * Apply ordering to the current model as per the sort parameters.
     */
    public function applySort()
    {
        parent::applySort();
    }

    /**
     * Default js action when saving form.
     *
     * @throws \atk4\core\Exception
     *
     * @return array
     */
    public function jsSave($notifier)
    {
        return [
            $this->factory($notifier, null, 'atk4\ui'),
            // reload Grid Container.
            $this->container->jsReload([$this->name.'_sort' => $this->getSortBy()]),
        ];
    }

    /**
     *  Return js statement necessary to remove a row in Grid when
     *  use in $(this) context.
     *
     * @return mixed
     */
    public function jsDelete()
    {
        return (new jQuery())->closest('tr')->transition('fade left');
    }
}
