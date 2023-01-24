<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;
use Atk4\Data\Model;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\UserAction\ExecutorInterface;

/**
 * A collection of Card set from a model.
 */
class CardDeck extends View
{
    public $ui = '';

    /** @var class-string<View> Card type inside this deck. */
    public $card = Card::class;

    /** @var string default template file. */
    public $defaultTemplate = 'card-deck.html';

    /** @var bool Whether card should use table display or not. */
    public $useTable = false;

    /** @var bool Whether card should use label display or not. */
    public $useLabel = false;

    /** @var string|null If using extra field in Card, glue, join them using extra glue. */
    public $extraGlue = ' - ';

    /** @var bool If each card should use action or not. */
    public $useAction = true;

    /** @var View|null The container view. The view that is reload when page or data changed. */
    public $container = [View::class, 'ui' => 'vertical segment'];

    /** @var View The view containing Cards. */
    public $cardHolder = [View::class, 'ui' => 'cards'];

    /** @var Paginator|false|null The paginator view. */
    public $paginator = [Paginator::class];

    /** @var int The number of cards to be displayed per page. */
    public $ipp = 9;

    /** @var Menu|false Will be initialized to Menu object, however you can set this to false to disable menu. */
    public $menu;

    /** @var array|VueComponent\ItemSearch|false */
    public $search = [VueComponent\ItemSearch::class];

    /** @var array Default notifier to perform when model action is successful * */
    public $notifyDefault = [JsToast::class, 'settings' => ['displayTime' => 5000]];

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

        $this->container = $this->add($this->container);

        if ($this->menu !== false && !is_object($this->menu)) {
            $this->menu = $this->add(Factory::factory([Menu::class, 'activateOnClick' => false], $this->menu), 'Menu');

            if ($this->search !== false) {
                $this->addMenuBarSeach();
            }
        }

        $this->cardHolder = $this->container->add($this->cardHolder);

        if ($this->paginator !== false) {
            $this->addPaginator();
            $this->stickyGet($this->paginator->name);
        }
    }

    protected function addMenuBarSeach(): void
    {
        $view = View::addTo($this->menu->addMenuRight()->addItem()->setElement('div'));

        $this->search = $view->add(Factory::factory($this->search, ['context' => $this->container]));
        $this->search->reload = $this->container;
        $this->query = $this->stickyGet($this->search->queryArg);
    }

    /**
     * Add Paginator view to card deck.
     */
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
                $c = $this->cardHolder->add(Factory::factory([$this->card], ['useLabel' => $this->useLabel, 'useTable' => $this->useTable]))->addClass('segment');
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
                $this->menuActions[$k]['btn'] = $this->menu->addItem(
                    $this->getExecutorFactory()->createTrigger($action, ExecutorFactory::MENU_ITEM)
                );
                $this->menuActions[$k]['executor'] = $executor;
            }
        }

        $this->setItemsAction();
    }

    /**
     * Setup js for firing menu action - copied from Crud - TODO deduplicate.
     */
    protected function setItemsAction(): void
    {
        foreach ($this->menuActions as $item) {
            // hack - render executor action via MenuItem::on() into container
            $item['btn']->on('click.atk_crud_item', $item['executor']);
            $jsAction = array_pop($item['btn']->_jsActions['click.atk_crud_item']);
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
     * Return proper js statement for afterExecute hook on action executor
     * depending on return type, model loaded and action scope.
     *
     * @param string|JsExpressionable|array<int, JsExpressionable>|Model|null $return
     *
     * @return JsExpressionable|array<int, JsExpressionable>
     */
    protected function jsExecute($return, Model\UserAction $action)
    {
        if (is_string($return)) {
            return $this->getNotifier($action, $return);
        } elseif (is_array($return) || $return instanceof JsExpressionable) {
            return $return;
        } elseif ($return instanceof Model) {
            if ($return->isEntity()) {
                $action = $action->getActionForEntity($return);
            }

            $msg = $return->isLoaded() ? $this->saveMsg : ($action->appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD ? $this->deleteMsg : $this->defaultMsg);

            return $this->jsModelReturn($action, $msg);
        }

        return $this->getNotifier($action, $this->defaultMsg);
    }

    /**
     * Override this method for setting notifier based on action or model value.
     */
    protected function getNotifier(Model\UserAction $action, string $msg = null): JsExpressionable
    {
        $notifier = Factory::factory($this->notifyDefault);
        if ($msg) {
            $notifier->setMessage($msg);
        }

        return $notifier;
    }

    /**
     * Js expression return when action afterHook executor return a Model.
     *
     * @return array<int, JsExpressionable>
     */
    protected function jsModelReturn(Model\UserAction $action, string $msg = 'Done!'): array
    {
        $js = [];
        $js[] = $this->getNotifier($action, $msg);
        $card = $action->getEntity()->isLoaded() ? $this->findCard($action->getEntity()) : null;
        if ($card !== null) {
            $js[] = $card->jsReload($this->getReloadArgs());
        } else {
            $js[] = $this->container->jsReload($this->getReloadArgs());
        }

        return $js;
    }

    /**
     * Check if a card is still in current set and
     * return it. Otherwise return null.
     * After an action is execute and data is saved, the db result
     * set might be different than previous one, which represent cards displayed on page.
     *
     * For example, editing a card which does not fulfill search requirement after it has been saved.
     * Or when adding a new one.
     * Therefore if card, that was just save, is not present in db result set or deck then return null
     * otherwise return Card view.
     *
     * @return View|null
     */
    protected function findCard(Model $entity)
    {
        $deck = [];
        foreach ($this->cardHolder->elements as $element) {
            if ($element instanceof $this->card) {
                $deck[$element->model->getId()] = $element;
            }
        }

        if ($entity->getModel()->tryLoad($entity->getId()) !== null) {
            // might be in result set but not in deck, for example when adding a card.
            return $deck[$entity->getId()] ?? null;
        }

        return null;
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
