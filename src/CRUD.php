<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

use atk4\data\UserAction\Action;
use atk4\data\UserAction\Generic;
use atk4\ui\ActionExecutor\UserAction;

/**
 * Implements a more sophisticated and interractive Data-Table component.
 */
class CRUD extends Grid
{
    /** @var array of fields to show */
    public $fieldsDefault = null;

    /** @var array of fields to show in grid */
    public $fieldsRead = null;

    /** @var array Default action to perform when adding or editing is successful * */
    public $notifyDefault = ['jsNotify', 'content' => 'Data is saved!', 'color'   => 'green'];

    /** @var array Action to perform when adding is successful * */
    public $notifyCreate = null;

    /** @var array Action to perform when editing is successful * */
    public $notifyUpdate = null;

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

        parent::setModel($m, $this->fieldsRead ?: $this->fieldsDefault);
        $this->model->unload();

        foreach ($m->getActions(Generic::SINGLE_RECORD) as $single_record_action) {
            $executor = $this->owner->factory($single_record_action->ui['executor'] ?? UserAction::class);
            $executor->addHook('afterExecute', function ($x) {
                return $this->container->jsReload();
                //var_dump($x);
            });

            $single_record_action->ui['executor'] = $executor;

            //$single_record_action->ui['executor'] = [UserAction::class, 'jsSuccess'=>];
            $this->addAction($single_record_action);
        }

        foreach ($m->getActions(Generic::NO_RECORDS) as $single_record_action) {
            $executor = $this->owner->add($single_record_action->ui['executor'] ?? UserAction::class);
            $executor->addHook('afterExecute', function ($x, $action_result) {
                if ($action_result === []) {
                    // row was deleted
                } else {
                    return $this->container->jsReload();
                }
            });
            $executor->setAction($single_record_action);

            $this->menu->addItem('add')->on('click', $single_record_action);

            //$single_record_action->ui['executor'] = [UserAction::class, 'jsSuccess'=>];
            //$this->addAction($single_record_action);
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
}
