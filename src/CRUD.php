<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

use atk4\data\Model;
use atk4\data\UserAction\Generic;
use atk4\ui\ActionExecutor\jsInterface_;
use atk4\ui\ActionExecutor\jsUserAction;
use atk4\ui\ActionExecutor\UserAction;

/**
 * Implements a more sophisticated and interactive Data-Table component.
 */
class CRUD extends Grid
{
    /** @var array of fields to display in Grid */
    public $displayFields = null;

    /** @var null|array of fields to edit in Form */
    public $editFields = null;

    /** @var array Default notifier to perform when adding or editing is successful * */
    public $notifyDefault = ['jsToast'];

    /** @var string default js action executor class in UI for model action. */
    public $jsExecutor = jsUserAction::class;

    /** @var string default action executor class in UI for model action. */
    public $executor = UserAction::class;

    /** @var bool|null should we use drop-down menu to display user actions? */
    public $useMenuActions = null;

    /** @var array Collection of NO_RECORDS Scope Model action menu item */
    private $menuItems = [];

    /** @var array Model single scope action to include in table action column. Will include all single scope actions if empty.*/
    public $singleScopeActions = [];

    /** @var array Model no_record scope action to include in menu. Will include all no record scope actions if empty. */
    public $noRecordScopeActions = [];

    /** @var string Message to display when record is add or edit successfully. */
    public $saveMsg = 'Record has been saved!';

    /** @var string Message to display when record is delete successfully. */
    public $deleteMsg = 'Record has been deleted!';

    /** @var string Generic display message for no record scope action where model is not loaded. */
    public $defaultMsg = 'Done!';

    public function init()
    {
        parent::init();

        if ($sortBy = $this->getSortBy()) {
            $this->app->stickyGet($this->name.'_sort', $sortBy);
        }

    }

    /**
     * Apply ordering to the current model as per the sort parameters.
     */
    public function applySort()
    {
        parent::applySort();

        if ($this->getSortBy() && !empty($this->menuItems)) {
            foreach ($this->menuItems as $k => $item) {
                //Remove previous click handler and attach new one using sort argument.
                $this->container->js(true, $item['item']->js()->off('click.atk_crud_item'));
                $ex = $item['action']->ui['executor'];
                if ($ex instanceof jsInterface_) {
                    $ex->stickyGet($this->name.'_sort', $this->getSortBy());
                    $this->container->js(true, $item['item']->js()->on('click.atk_crud_item', new jsFunction($ex->jsExecute())));
                }
            }
        }
    }

    /**
     * Sets data model of CRUD.
     *
     * @param \atk4\data\Model $m
     * @param null|array       $fields
     *
     * @throws \atk4\core\Exception
     * @throws Exception
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $m, $fields = null) : \atk4\data\Model
    {
        if ($fields !== null) {
            $this->displayFields = $fields;
        }

        parent::setModel($m, $this->displayFields);

        $this->model->unload();

        if (is_null($this->useMenuActions)) {
            $this->useMenuActions = count($m->getActions()) > 4;
        }

        foreach ($this->_getModelActions(Generic::SINGLE_RECORD) as $action) {
            $executor = $this->getActionExecutor($action);
            $action->fields = $this->_getActionFields($action);
            $action->ui['executor'] = $executor;
            $executor->addHook('afterExecute', function ($ex, $return, $id) use ($action) {
                return $this->getJsExecute($return, $action);
            });
            if ($this->useMenuActions) {
                $this->addActionMenuItem($action);
            } else {
                $this->addAction($action);
            }
        }

        if ($this->menu) {
            foreach ($this->_getModelActions(Generic::NO_RECORDS) as $k => $action) {
                $executor = $this->getActionExecutor($action);
                if ($executor instanceof View) {
                    $executor->stickyGet($this->name.'_sort', $this->getSortBy());
                }
                $action->fields = $this->_getActionFields($action);
                $action->ui['executor'] = $executor;
                $executor->addHook('afterExecute', function ($ex, $return, $id) use ($action) {
                    return $this->getJsExecute($return, $action);
                });
                $this->menuItems[$k]['item'] = $this->menu->addItem([$action->getDescription(), 'icon' => 'plus']);
                $this->menuItems[$k]['action'] = $action;
            }
            $this->setItemsAction();
        }

        return $this->model;
    }

    /**
     * Return proper js statement for afterExecute hook on action executor
     * depending on return type, model loaded and action scope.
     *
     * @param $return
     * @param $action
     *
     * @return array|object
     * @throws \atk4\core\Exception
     */
    protected function getJsExecute($return, $action)
    {
        if (is_string($return)) {
            return  $this->getJsNotify($this->notifyDefault, $return, $action);
        } elseif (is_array($return) || $return instanceof jsExpressionable) {
            return $return;
        } elseif ($return instanceof Model) {
            $msg = $return->loaded() ? $this->saveMsg : ($action->scope === Generic::SINGLE_RECORD ? $this->deleteMsg : $this->defaultMsg);
            return $this->jsModelReturn($action, $msg);
        } else {
            return $this->getJsNotify($this->notifyDefault, $this->defaultMsg, $action);
        }
    }

    /**
     * Return jsNotifier object.
     * Override this method for setting notifier based on action or model value.
     *
     * @param array        $notifier_seed Notifier Object seed.
     * @param null|string  $msg           The message to display.
     * @param null|Generic $action        The action short name.
     *
     * @throws \atk4\core\Exception
     *
     * @return object
     */
    protected function getJsNotify($notifier_seed, $msg = null, $action = null)
    {
        $notifier = $this->factory($notifier_seed, null, 'atk4\ui');
        if ($msg) {
            $notifier->setMessage($msg);
        }

        return $notifier;
    }

    /**
     * js expression return when action afterHook executor return a Model.
     *
     * @param Model $model
     * @param null $action
     * @param string $msg
     *
     * @return array
     * @throws \atk4\core\Exception
     */
    protected function jsModelReturn(Generic $action = null, $msg = 'Done!')
    {
        $js[] = $this->getJsNotify($this->notifyDefault, $msg, $action);
        $js[] = $action->owner->loaded() ? $this->container->jsReload($this->_getReloadArgs()) : (new jQuery())->closest('tr')->transition('fade left');

        return $js;
    }

    /**
     * Setup js for firing menu action.
     *
     * @throws \atk4\core\Exception
     */
    protected function setItemsAction()
    {
        foreach ($this->menuItems as $k => $item) {
            $this->container->js(true, $item['item']->on('click.atk_crud_item', $item['action']));
        }
    }

    /**
     * Return proper action executor base on model action.
     *
     * @param \atk4\data\UserAction\Generic $action
     *
     * @throws \atk4\core\Exception
     *
     * @return object
     */
    protected function getActionExecutor(\atk4\data\UserAction\Generic $action)
    {
        if (isset($action->ui['executor'])) {
            return $this->factory($action->ui['executor']);
        }

        $executor = (!$action->args && !$action->fields && !$action->preview) ? $this->jsExecutor : $this->executor;

        return $this->factory($executor);
    }

    /**
     * Set proper action fields based on executor and action.
     * Prioritizing $this->editFields over action->field.
     *
     * @param $action
     *
     * @return array|bool
     */
    private function _getActionFields($action)
    {
        $fields = false;
        if ($this->editFields) {
            $fields = $this->editFields;
        } elseif (is_array($action->fields) && !empty($action->fields)) {
            $fields = $action->fields;
        } elseif ($action->fields === true) {
            $fields = true;
        }
        return $fields;
    }

    /**
     * Return reload argument based on CRUD condition.
     *
     * @return mixed
     */
    private function _getReloadArgs()
    {
        $args[$this->name.'_sort'] =  $this->getSortBy();
        if ($this->paginator) {
            $args[$this->paginator->name] = $this->paginator->getCurrentPage();
        }

        return $args;
    }

    /**
     * Return proper action need to setup menu or action column.
     *
     * @param string $scope
     *
     * @return array
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    private function _getModelActions(string $scope) : array
    {
        $actions = [];
        if ($scope === Generic::SINGLE_RECORD && !empty($this->singleScopeActions)) {
            foreach ($this->singleScopeActions as $action) {
                $actions[]  = $this->model->getAction($action);
            }
        } elseif ($scope === Generic::NO_RECORDS && !empty($this->noRecordScopeActions)) {
            foreach ($this->noRecordScopeActions as $action) {
                $actions[]  = $this->model->getAction($action);
            }
        } else {
            $actions = $this->model->getActions($scope);
        }

        return $actions;
    }

    /**
     * Set callback for edit action in CRUD.
     * Callback function will receive the Edit Form and Executor as param.
     *
     * @param callable $fx
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    public function onEditAction(callable $fx)
    {
        $this->setOnActionForm($fx, 'edit');
    }

    /**
     * Set callback for add action in CRUD.
     * Callback function will receive the Edit Form and Executor as param.
     *
     * @param callable $fx
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    public function onAddAction(callable $fx)
    {
        $this->setOnActionForm($fx, 'add');
    }

    /**
     * Set callback for both edit and add action form.
     * Callback function will receive Forms and Executor as param.
     *
     * @param callable $fx
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    public function onAction(callable $fx)
    {
        $this->onEditAction($fx);
        $this->onAddAction($fx);
    }

    /**
     * Set onAction callback using UserAction executor.
     *
     * @param callable $fx
     * @param string   $actionName
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     *
     * @return null|mixed
     */
    public function setOnActionForm(callable $fx, string $actionName)
    {
        if (!$this->model) {
            throw new Exception('Model need to be set prior to use on Form');
        }

        $ex = $this->model->getAction($actionName)->ui['executor'];
        if ($ex && $ex instanceof UserAction) {
            $ex->addHook('onStep', function ($ex, $step, $form) use ($fx) {
                if ($step === 'fields') {
                    return call_user_func($fx, $form, $ex);
                }
            });
        }
    }
}
