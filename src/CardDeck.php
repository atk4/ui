<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\UserAction\ExecutorInterface;
use Atk4\Ui\UserAction\SharedExecutorsContainer;

/**
 * A collection of Card set from a model.
 */
class CardDeck extends View
{
    public $ui = 'basic segment atk-card-deck';

    public $defaultTemplate = 'card-deck.html';

    /** @var class-string<View> Card type inside this deck. */
    public $card = Card::class;

    /** @var bool Whether card should use table display or not. */
    public $useTable = false;

    /** @var bool Whether card should use label display or not. */
    public $useLabel = false;

    /** @var string|null If using extra field in Card, glue, join them using extra glue. */
    public $extraGlue = ' - ';

    /** @var bool If each card should use action or not. */
    public $useAction = true;

    /** @var SharedExecutorsContainer|null */
    public $sharedExecutorsContainer = [SharedExecutorsContainer::class];

    /** @var View|null The container view. The view that is reload when page or data changed. */
    public $container = [View::class, 'ui' => 'vertical segment'];

    /** @var View The view containing Cards. */
    public $cardHolder = [View::class, 'ui' => 'cards'];

    /** @var Paginator|false|null The paginator view. */
    public $paginator = [Paginator::class];

    /** @var int The number of cards to be displayed per page. */
    public $ipp = 9;

    /** @var Menu|array|false Will be initialized to Menu object, however you can set this to false to disable menu. */
    public $menu;

    /** @var array|VueComponent\ItemSearch|false */
    public $search = [VueComponent\ItemSearch::class];

    /** @var array Default notifier to perform when model action is successful * */
    public $notifyDefault = [JsToast::class];

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

    /** @var array seed to create View for displaying when search result is empty. */
    public $noRecordDisplay = [
        Message::class,
        'content' => 'Result empty!',
        'icon' => 'info circle',
        'text' => 'Your search did not return any record or there is no record available.',
    ];

    /** @var array A collection of menu button added in Menu. */
    private $menuActions = [];

    /** @var string|null The current search query string. */
    private $query;

    protected function init(): void
    {
        parent::init();

        $this->sharedExecutorsContainer = $this->add($this->sharedExecutorsContainer);

        $this->container = $this->add($this->container);

        if ($this->menu !== false && !is_object($this->menu)) {
            $this->menu = $this->add(Factory::factory([Menu::class, 'activateOnClick' => false], $this->menu), 'Menu');

            if ($this->search !== false) {
                $this->addMenuBarSearch();
            }
        }

        $this->cardHolder = $this->container->add($this->cardHolder);

        if ($this->paginator !== false) {
            $this->addPaginator();
            $this->stickyGet($this->paginator->name);
        }
    }

    protected function addMenuBarSearch(): void
    {
        $view = View::addTo($this->menu->addMenuRight()->addItem()->setElement('div'));

        $this->search = $view->add(Factory::factory($this->search, ['context' => $this->container]));
        $this->search->reload = $this->container;
        $this->query = $this->stickyGet($this->search->queryArg);
    }

    protected function addPaginator(): void
    {
        $seg = View::addTo($this->container, ['ui' => 'basic segment'])->setStyle('text-align', 'center');
        $this->paginator = $seg->add(Factory::factory($this->paginator, ['reload' => $this->container]));
    }

    /**
     * @param array<int, string>|null $fields
     */
    public function setModel(Model $model, array $fields = null, array $extra = null): void
    {
        parent::setModel($model);

        if ($this->search !== false) {
            $this->search->setModelCondition($this->model);
        }

        $count = $this->initPaginator();
        if ($count) {
            foreach ($this->model as $m) {
                /** @var Card */
                $c = $this->cardHolder->add(Factory::factory([$this->card], ['useLabel' => $this->useLabel, 'useTable' => $this->useTable]));
                $c->setModel($m, $fields);
                if ($extra) {
                    $c->addExtraFields($m, $extra, $this->extraGlue);
                }
                if ($this->useAction) {
                    foreach ($this->getModelActions(Model\UserAction::APPLIES_TO_SINGLE_RECORD) as $action) {
                        $c->addClickAction($action, null, $this->getReloadArgs());
                    }
                }
            }
        } else {
            $this->cardHolder->addClass('centered')->add(Factory::factory($this->noRecordDisplay));
        }

        // add no record scope action to menu
        if ($this->useAction && $this->menu) {
            foreach ($this->getModelActions(Model\UserAction::APPLIES_TO_NO_RECORDS) as $k => $action) {
                $executor = $this->initActionExecutor($action);
                $this->menuActions[$k]['button'] = $this->menu->addItem(
                    $this->getExecutorFactory()->createTrigger($action, ExecutorFactory::MENU_ITEM)
                );
                $this->menuActions[$k]['executor'] = $executor;
            }
        }

        $this->setItemsAction();
    }

    /**
     * Setup JS for firing menu action - copied from Crud - TODO deduplicate.
     */
    protected function setItemsAction(): void
    {
        foreach ($this->menuActions as $item) {
            // hack - render executor action via MenuItem::on() into container
            $item['button']->on('click.atk_crud_item', $item['executor']);
            $jsAction = array_pop($item['button']->_jsActions['click.atk_crud_item']);
            $this->container->js(true, $jsAction);
        }
    }

    /**
     * Setup executor for an action.
     * First determine what fields action needs,
     * then setup executor based on action fields, args and/or preview.
     *
     * Single record scope action use jsSuccess instead of afterExecute hook
     * because hook will keep adding for every cards, thus repeating jsExecute multiple time,
     * i.e. once for each card, unless hook is break.
     */
    protected function initActionExecutor(Model\UserAction $action): ExecutorInterface
    {
        $executor = $this->getExecutorFactory()->createExecutor($action, $this);
        if ($action->appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD) {
            $executor->jsSuccess = function (ExecutorInterface $ex, Model $m, $id, $return) use ($action) {
                return $this->jsExecute($return, $action);
            };
        } else {
            $executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function (ExecutorInterface $ex, $return, $id) use ($action) {
                return $this->jsExecute($return, $action);
            });
        }

        return $executor;
    }

    /**
     * Return proper JS statement for afterExecute hook on action executor
     * depending on return type, model loaded and action scope.
     *
     * @param string|JsExpressionable|Model|null $return
     */
    protected function jsExecute($return, Model\UserAction $action): JsBlock
    {
        $res = new JsBlock();

        if ($return instanceof Model) {
            $return = $return->isLoaded()
                ? $this->saveMsg
                : ($action->appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD ? $this->deleteMsg : $this->defaultMsg);
        }

        if (is_string($return)) {
            $msg = $this->jsCreateNotifier($action, $return);
        } elseif ($return instanceof JsExpressionable) {
            $msg = $return;
        } else {
            $msg = $this->jsCreateNotifier($action, $this->defaultMsg);
        }
        $res->addStatement($msg);

        $res->addStatement($this->container->jsReload($this->getReloadArgs()));

        return $res;
    }

    /**
     * Override this method for setting notifier based on action or model value.
     */
    protected function jsCreateNotifier(Model\UserAction $action, string $msg = null): JsBlock
    {
        $notifier = Factory::factory($this->notifyDefault);
        if ($msg) {
            $notifier->setMessage($msg);
        }

        return new JsBlock([$notifier]);
    }

    /**
     * Return reload argument based on Deck condition.
     *
     * @return mixed
     */
    private function getReloadArgs()
    {
        $args = [];
        if ($this->paginator !== false) {
            $args[$this->paginator->name] = $this->paginator->getCurrentPage();
        }
        if ($this->search !== false) {
            $args[$this->search->queryArg] = $this->query;
        }

        return $args;
    }

    /**
     * Return proper action need to setup menu or action column.
     */
    private function getModelActions(string $appliesTo): array
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
     * Will set model limit according to paginator value.
     */
    protected function initPaginator(): int
    {
        $count = $this->model->executeCountQuery();
        if ($this->paginator) {
            if ($count > 0) {
                $this->paginator->setTotal((int) ceil($count / $this->ipp));
                $this->model->setLimit($this->ipp, ($this->paginator->page - 1) * $this->ipp);
            } else {
                $this->paginator->destroy();
            }
        }

        return $count;
    }
}
