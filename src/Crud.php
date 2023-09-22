<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\UserAction\ExecutorInterface;

class Crud extends Grid
{
    /** @var array of fields to display in Grid */
    public $displayFields;

    /** @var array|null of fields to edit in Form for Model edit action */
    public $editFields;

    /** @var array|null of fields to edit in Form for Model add action */
    public $addFields;

    /** @var array Default notifier to perform when adding or editing is successful * */
    public $notifyDefault = [JsToast::class];

    /** @var bool|null should we use table column drop-down menu to display user actions? */
    public $useMenuActions;

    /** @var array<string, array{item: MenuItem, executor: AbstractView&ExecutorInterface}> Collection of APPLIES_TO_NO_RECORDS Scope Model action menu item */
    private array $menuItems = [];

    /** Model single scope action to include in table action column. Will include all single scope actions if empty. */
    public array $singleScopeActions = [];

    /** Model no_record scope action to include in menu. Will include all no record scope actions if empty. */
    public array $noRecordScopeActions = [];

    /** @var string Message to display when record is add or edit successfully. */
    public $saveMsg = 'Record has been saved!';

    /** @var string Message to display when record is delete successfully. */
    public $deleteMsg = 'Record has been deleted!';

    /** @var string Generic display message for no record scope action where model is not loaded. */
    public $defaultMsg = 'Done!';

    /** @var array<int, array<string, \Closure(Form, UserAction\ModalExecutor): void>> Callback containers for model action. */
    public $onActions = [];

    /** @var mixed recently deleted record ID. */
    private $deletedId;

    protected function init(): void
    {
        parent::init();

        $sortBy = $this->getSortBy();
        if ($sortBy) {
            $this->stickyGet($this->name . '_sort', $sortBy);
        }
    }

    public function applySort(): void
    {
        parent::applySort();

        if ($this->getSortBy()) {
            foreach ($this->menuItems as $item) {
                // remove previous click handler and attach new one using sort argument
                $this->container->js(true, $item['item']->js()->off('click.atk_crud_item'));
                $ex = $item['executor'];
                if ($ex instanceof UserAction\JsExecutorInterface) {
                    $ex->stickyGet($this->name . '_sort', $this->getSortBy());
                    $this->container->js(true, $item['item']->js()->on('click.atk_crud_item', new JsFunction([], $ex->jsExecute([]))));
                }
            }
        }
    }

    public function setModel(Model $model, array $fields = null): void
    {
        $model->assertIsModel();

        if ($fields !== null) {
            $this->displayFields = $fields;
        }

        parent::setModel($model, $this->displayFields);

        // grab model ID when using delete
        // must be set before delete action execute
        $this->model->onHook(Model::HOOK_AFTER_DELETE, function (Model $model) {
            $this->deletedId = $model->getId();
        });

        if ($this->useMenuActions === null) {
            $this->useMenuActions = count($model->getUserActions()) > 4;
        }

        foreach ($this->_getModelActions(Model\UserAction::APPLIES_TO_SINGLE_RECORD) as $action) {
            $executor = $this->initActionExecutor($action);
            if ($this->useMenuActions) {
                $this->addExecutorMenuItem($executor);
            } else {
                $this->addExecutorButton($executor);
            }
        }

        if ($this->menu) {
            foreach ($this->_getModelActions(Model\UserAction::APPLIES_TO_NO_RECORDS) as $k => $action) {
                if ($action->enabled) {
                    $executor = $this->initActionExecutor($action);
                    $this->menuItems[$k]['item'] = $this->menu->addItem(
                        $this->getExecutorFactory()->createTrigger($action, ExecutorFactory::MENU_ITEM)
                    );
                    $this->menuItems[$k]['executor'] = $executor;
                }
            }
            $this->setItemsAction();
        }
    }

    /**
     * Setup executor for an action.
     * First determine what fields action needs,
     * then setup executor based on action fields, args and/or preview.
     *
     * Add hook for onStep 'fields'" Hook can call a callback function
     * for UserAction onStep field. Callback will receive executor form where you
     * can setup Input field via javascript prior to display form or change form submit event
     * handler.
     *
     * @return AbstractView&ExecutorInterface
     */
    protected function initActionExecutor(Model\UserAction $action)
    {
        $executor = $this->getExecutor($action);
        $executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function (ExecutorInterface $ex, $return, $id) use ($action) {
            return $this->jsExecute($return, $action);
        });

        if ($executor instanceof UserAction\ModalExecutor) {
            foreach ($this->onActions as $onAction) {
                $executor->onHook(UserAction\ModalExecutor::HOOK_STEP, static function (UserAction\ModalExecutor $ex, string $step, Form $form) use ($onAction, $action) {
                    $key = array_key_first($onAction);
                    if ($key === $action->shortName && $step === 'fields') {
                        $onAction[$key]($form, $ex);
                    }
                });
            }
        }

        return $executor;
    }

    /**
     * Return proper JS statement for afterExecute hook on action executor
     * depending on return type, model loaded and action scope.
     *
     * @param string|null $return
     */
    protected function jsExecute($return, Model\UserAction $action): JsBlock
    {
        $res = new JsBlock();
        $jsAction = $this->getJsGridAction($action);
        if ($jsAction) {
            $res->addStatement($jsAction);
        }

        // display msg return by action or depending on action modifier
        if (is_string($return)) {
            $res->addStatement($this->jsCreateNotifier($return));
        } else {
            if ($action->modifier === Model\UserAction::MODIFIER_CREATE || $action->modifier === Model\UserAction::MODIFIER_UPDATE) {
                $res->addStatement($this->jsCreateNotifier($this->saveMsg));
            } elseif ($action->modifier === Model\UserAction::MODIFIER_DELETE) {
                $res->addStatement($this->jsCreateNotifier($this->deleteMsg));
            } else {
                $res->addStatement($this->jsCreateNotifier($this->defaultMsg));
            }
        }

        return $res;
    }

    /**
     * Return proper JS actions depending on action modifier type.
     */
    protected function getJsGridAction(Model\UserAction $action): ?JsExpressionable
    {
        switch ($action->modifier) {
            case Model\UserAction::MODIFIER_UPDATE:
            case Model\UserAction::MODIFIER_CREATE:
                $js = $this->container->jsReload($this->_getReloadArgs());

                break;
            case Model\UserAction::MODIFIER_DELETE:
                // use deleted record ID to remove row, fallback to closest tr if ID is not available
                $js = $this->deletedId
                    ? $this->js(false, null, 'tr[data-id="' . $this->deletedId . '"]')
                    : (new Jquery())->closest('tr');
                $js = $js->transition('fade left', new JsFunction([], [new JsExpression('this.remove()')]));

                break;
            default:
                $js = null;
        }

        return $js;
    }

    /**
     * Override this method for setting notifier based on action or model value.
     */
    protected function jsCreateNotifier(string $msg = null): JsExpressionable
    {
        $notifier = Factory::factory($this->notifyDefault);
        if ($msg) {
            $notifier->setMessage($msg);
        }

        return $notifier;
    }

    /**
     * Setup JS for firing menu action.
     */
    protected function setItemsAction(): void
    {
        foreach ($this->menuItems as $item) {
            // hack - render executor action via MenuItem::on() into container
            $item['item']->on('click.atk_crud_item', $item['executor']);
            $jsAction = array_pop($item['item']->_jsActions['click.atk_crud_item']);
            $this->container->js(true, $jsAction);
        }
    }

    /**
     * Return proper action executor base on model action.
     *
     * @return AbstractView&ExecutorInterface
     */
    protected function getExecutor(Model\UserAction $action)
    {
        // prioritize Crud addFields over action->fields for Model add action
        if ($action->shortName === 'add' && $this->addFields) {
            $action->fields = $this->addFields;
        }

        // prioritize Crud editFields over action->fields for Model edit action
        if ($action->shortName === 'edit' && $this->editFields) {
            $action->fields = $this->editFields;
        }

        return $this->getExecutorFactory()->createExecutor($action, $this);
    }

    /**
     * Return reload argument based on Crud condition.
     *
     * @return mixed
     */
    private function _getReloadArgs()
    {
        $args = [];
        $args[$this->name . '_sort'] = $this->getSortBy();
        if ($this->paginator) {
            $args[$this->paginator->name] = $this->paginator->getCurrentPage();
        }

        return $args;
    }

    /**
     * Return proper action need to setup menu or action column.
     */
    private function _getModelActions(string $appliesTo): array
    {
        if ($appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD && $this->singleScopeActions !== []) {
            $actions = array_map(fn ($v) => $this->model->getUserAction($v), $this->singleScopeActions);
        } elseif ($appliesTo === Model\UserAction::APPLIES_TO_NO_RECORDS && $this->noRecordScopeActions !== []) {
            $actions = array_map(fn ($v) => $this->model->getUserAction($v), $this->noRecordScopeActions);
        } else {
            $actions = $this->model->getUserActions($appliesTo);
        }

        return $actions;
    }

    /**
     * Set callback for add action in Crud.
     * Callback function will receive the Add Form and Executor as param.
     *
     * @param \Closure(Form, UserAction\ModalExecutor): void $fx
     */
    public function onFormAdd(\Closure $fx): void
    {
        $this->setOnActions('add', $fx);
    }

    /**
     * Set callback for edit action in Crud.
     * Callback function will receive the Edit Form and Executor as param.
     *
     * @param \Closure(Form, UserAction\ModalExecutor): void $fx
     */
    public function onFormEdit(\Closure $fx): void
    {
        $this->setOnActions('edit', $fx);
    }

    /**
     * Set callback for both edit and add action form.
     * Callback function will receive Forms and Executor as param.
     *
     * @param \Closure(Form, UserAction\ModalExecutor): void $fx
     */
    public function onFormAddEdit(\Closure $fx): void
    {
        $this->onFormAdd($fx);
        $this->onFormEdit($fx);
    }

    /**
     * Set onActions.
     *
     * @param \Closure(Form, UserAction\ModalExecutor): void $fx
     */
    public function setOnActions(string $actionName, \Closure $fx): void
    {
        $this->onActions[] = [$actionName => $fx];
    }
}
