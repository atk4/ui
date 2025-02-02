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
    /** @var list<string> Fields to display in Grid */
    public $displayFields;

    /** @var list<string>|null Fields to edit in Form for Model edit action */
    public $editFields;

    /** @var list<string>|null Fields to edit in Form for Model add action */
    public $addFields;

    /** @var array<mixed> Default notifier to perform when adding or editing is successful * */
    public $notifyDefault = [JsToast::class];

    /** @var bool|null Should we use table column drop-down menu to display user actions? */
    public $useMenuActions;

    /** @var array<string, array{item: MenuItem, executor: AbstractView&ExecutorInterface}> Collection of APPLIES_TO_NO_RECORD Scope Model action menu item */
    private array $menuItems = [];

    /** @var list<string> Model single scope action to include in table action column. Will include all single scope actions if empty. */
    public array $singleScopeActions = [];

    /** @var list<string> Model no_record scope action to include in menu. Will include all no record scope actions if empty. */
    public array $noRecordScopeActions = [];

    /** @var string Message to display when record is add or edit successfully. */
    public $saveMsg = 'Record has been saved!';

    /** @var string Message to display when record is delete successfully. */
    public $deleteMsg = 'Record has been deleted!';

    /** @var string Generic display message for no record scope action where model is not loaded. */
    public $defaultMsg = 'Done!';

    /** @var list<array<string, \Closure(Form, UserAction\ModalExecutor): void>> Callback containers for model action. */
    public array $onActions = [];

    /** @var mixed Recently created/updated record ID. */
    private $updatedId;

    /** @var mixed Recently deleted record ID. */
    private $deletedId;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $sortBy = $this->getSortBy();
        if ($sortBy) {
            $this->stickyGet($this->name . '_sort', $sortBy);
        }
    }

    #[\Override]
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

    #[\Override]
    public function setModel(Model $model, ?array $fields = null): void
    {
        if ($fields !== null) {
            $this->displayFields = $fields;
        }

        parent::setModel($model, $this->displayFields);

        $this->model->onHook(Model::HOOK_BEFORE_SAVE, function (Model $entity) {
            $this->updatedId = $entity->getId();
        });
        $this->model->onHook(Model::HOOK_AFTER_SAVE, function (Model $entity) {
            $this->updatedId = $entity->getId();
        });
        $this->model->onHook(Model::HOOK_AFTER_DELETE, function (Model $entity) {
            $this->updatedId = null;
            $this->deletedId = $entity->getId();
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
            foreach ($this->_getModelActions(Model\UserAction::APPLIES_TO_NO_RECORD) as $k => $action) {
                $item = $this->menu->addItem(
                    $this->getExecutorFactory()->createTrigger($action, ExecutorFactory::MENU_ITEM)
                );

                if ($action->enabled === false) {
                    $item->addClass('disabled');
                }

                $this->menuItems[$k] = [
                    'item' => $item,
                    'executor' => $this->initActionExecutor($action),
                ];
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
        $jsAction = $this->jsGridAction($action);
        if ($jsAction) {
            $res->addStatement($jsAction);
        }

        // display msg return by action or depending on action behavior
        if (is_string($return)) {
            $res->addStatement($this->jsCreateNotifier($return));
        } else {
            if ($this->updatedId !== null) {
                $res->addStatement($this->jsCreateNotifier($this->saveMsg));
            } elseif ($this->deletedId !== null) {
                $res->addStatement($this->jsCreateNotifier($this->deleteMsg));
            } else {
                $res->addStatement($this->jsCreateNotifier($this->defaultMsg));
            }
        }

        return $res;
    }

    /**
     * Return proper JS actions depending on action behavior.
     */
    protected function jsGridAction(Model\UserAction $action): ?JsExpressionable
    {
        if ($this->updatedId !== null) {
            $js = $this->container->jsReload($this->_getReloadArgs());
        } elseif ($this->deletedId !== null) {
            // use deleted record ID to remove row, fallback to closest tr if ID is not available
            $js = $this->deletedId
                ? $this->js(false, null, 'tr[data-id="' . $this->getApp()->uiPersistence->typecastAttributeSaveField($this->model->getIdField(), $this->deletedId) . '"]')
                : (new Jquery())->closest('tr');
            $js = $js->transition('fade left', new JsFunction([], [new JsExpression('this.remove()')]));
        } else {
            $js = null;
        }

        return $js;
    }

    /**
     * Override this method for setting notifier based on action or model value.
     */
    protected function jsCreateNotifier(?string $msg = null): JsExpressionable
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
     *
     * @return array<string, Model\UserAction>
     */
    private function _getModelActions(string $appliesTo): array
    {
        if ($appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD && $this->singleScopeActions !== []) {
            $actions = array_combine(
                $this->singleScopeActions,
                array_map(fn ($v) => $this->model->getUserAction($v), $this->singleScopeActions)
            );
        } elseif ($appliesTo === Model\UserAction::APPLIES_TO_NO_RECORD && $this->noRecordScopeActions !== []) {
            $actions = array_combine(
                $this->noRecordScopeActions,
                array_map(fn ($v) => $this->model->getUserAction($v), $this->noRecordScopeActions)
            );
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
