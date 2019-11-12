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
    /** @var array of fields to show */
    public $fieldsDefault = null;

    /** @var array Default action to perform when adding or editing is successful * */
    public $notifyDefault = ['jsToast', 'settings'=> ['message' => 'Data is saved!', 'class' => 'success']];

    public $jsExecutor = jsUserAction::class;
    public $executor = UserAction::class;
    public $deleteMsg = 'Are you sure?';

    /**
     * Sets data model of CRUD.
     *
     * @param \atk4\data\Model $m
     * @param array            $defaultFields
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $m, $defaultFields = null)
    {
        if ($defaultFields !== null) {
            $this->fieldsDefault = $defaultFields;
        }

        parent::setModel($m);

        $this->model->unload();

        foreach ($m->getActions(Generic::SINGLE_RECORD) as $single_record_action) {
            if ($single_record_action->short_name === 'edit') {
                $executor = $this->owner->factory($single_record_action->ui['executor'] ?? $this->executor);

                $single_record_action->ui['executor'] = $executor;
                $single_record_action->fields = $this->fieldsDefault ?? true;
                $executor->addHook('afterExecute', function ($x) {
                    return $this->jsSave($this->notifyDefault);
                });
                $this->addAction(['icon'=>'edit'], $single_record_action);
            } elseif ($single_record_action->short_name === 'delete') {
                $executor = $this->owner->factory($single_record_action->ui['executor'] ?? $this->jsExecutor);
                $single_record_action->ui['executor'] = $executor;
                $single_record_action->ui['confirm'] = $this->deleteMsg;

                $executor->addHook('afterExecute', function ($x) {
                    return (new jQuery())->closest('tr')->transition('fade left');
                });
                $this->addAction(['icon'=>'trash'], $single_record_action);
            } else {
                $this->addActionMenuItem($single_record_action);
            }
        }

        foreach ($m->getActions(Generic::NO_RECORDS) as $single_record_action) {
            if ($single_record_action->short_name === 'add') {
                if (!$this->menu) {
                    throw new Exception('Can not add create button without menu');
                }
                $executor = $this->owner->factory($single_record_action->ui['executor'] ?? $this->executor);
                $single_record_action->ui['executor'] = $executor;
                $single_record_action->fields = $this->fieldsDefault ?? true;
                $executor->addHook('afterExecute', function ($x) {
                    return $this->jsSave($this->notifyDefault);
                });

                $btn = $this->menu->addItem(['Add new '.$this->model->getModelCaption(), 'icon' => 'plus']);
                $btn->on('click.atk_CRUD', $single_record_action, [$this->name.'_sort' => $this->getSortBy()]);
            }
        }

        return $this->model;
    }

    /**
     * Apply ordering to the current model as per the sort parameters.
     */
    public function applySort()
    {
        parent::applySort();

        if ($this->getSortBy() && $this->itemCreate) {
            //Remove previous click handler to Add new Item button and attach new one using sort argument.
            $this->container->js(true, $this->itemCreate->js()->off('click.atk_CRUD'));
            $this->container->js(true,
                                 $this->itemCreate->js()->on('click.atk_CRUD',
                                 new jsFunction([
                                     new jsModal('Add new', $this->pageCreate, [$this->name.'_sort' => $this->getSortBy()]),
                                 ]))
            );
        }
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
            // close modal
//            new jsExpression('$(".atk-dialog-content").trigger("close")'),

            // display notification
            $this->factory($notifier, null, 'atk4\ui'),

            // reload Grid Container.
            $this->container->jsReload([$this->name.'_sort' => $this->getSortBy()]),
        ];
    }
}
