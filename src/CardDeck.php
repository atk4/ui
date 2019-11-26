<?php
/**
 * A collection of Card set from a model.
 */

namespace atk4\ui;

use atk4\data\Model;
use atk4\data\UserAction\Generic;
use atk4\ui\ActionExecutor\jsUserAction;
use atk4\ui\ActionExecutor\UserAction;

class CardDeck extends View
{
    public $ui = '';

    /** @var string Card type inside this deck. */
    public $card = Card::class;

    /** @var string Template file for card container. */
    public $cardDeckTemplate = 'card-deck.html';

    /** @var bool Whether card should use table display or not. */
    public $useTable = false;

    /** @var bool Whether card should use label display or not. */
    public $useLabel = false;

    /** @var null|string If using extra field in Card, glue, join them using extra glue. */
    public $extraGlue = ' - ';

    /** @var bool If each card should use action or not. */
    public $useAction = true;

    /** @var null|View The container view. */
    public $container = null;

    /** @var null|View The paginator view. */
    public $paginator = null;

    /** @var int The number of card to be display per page. */
    public $ipp = 6;

    /** @var array  */
    public $menu = null;

    public $menuBtnStyle = 'primary';

    public $btns = null;

    /** @var null|int The current page number. */
    private $page = null;

    /** @var string Default executor class. */
    public $executor = UserAction::class;

    /** @var string Default jsExecutor class. */
    public $jsExecutor = jsUserAction::class;

    /** @var array Default notifier to perform when model action is successful * */
    public $notifyDefault = ['jsToast'];

    public $saveMsg = 'Record has been saved!';
    public $deleteMsg = 'Record has been deleted!';

    public function init()
    {
        parent::init();

        if ($this->menu !== false) {
            $this->menu = $this->add($this->factory(['View'], $this->menu, 'atk4\ui'));
            $this->btns = $this->menu->add(['ui' => 'buttons']);
        }

        $this->container = $this->add(['defaultTemplate' => $this->cardDeckTemplate]);

        $this->cardHolder = $this->container->add(['ui' => 'cards']);

        if ($this->paginator !== false) {
            $seg = $this->container->add(['View', 'ui'=> 'basic segment'], 'Paginator')->addStyle('text-align', 'center');
            $this->paginator = $seg->add($this->factory(['Paginator', 'reload' => $this->container], $this->paginator, 'atk4\ui'));
            $this->page = $this->app->stickyGet($this->paginator->name);
        }
    }

    public function setModel(Model $model, array $fields = null, array $extra = null)
    {
        parent::setModel($model);

        $this->_setModelLimitFromPaginator();

        $this->model->each(function ($m) use ($fields, $extra) {
            $c = $this->cardHolder->add([$this->card]);
            $c->setModel($m);
            $c->addSection($m->getTitle(), $m, $fields, $this->useLabel, $this->useTable);
            if ($extra) {
                $c->addExtraFields($m, $extra, $this->extraGlue);
            }
            if ($this->useAction) {
                if ($singleActions = $m->getActions(Generic::SINGLE_RECORD)) {
                    $page_arg = [$this->paginator->name => $this->page];
                    $id_arg = [];
                    foreach ($singleActions as $action) {
                        $ex = $this->getActionExecutor($action);
                        $ex->jsSuccess = function ($x, $m, $id, $return) use ($action, $c) {
                            // set action response depending on the return
                            if (is_string($return)) {

                                return  $this->getJsNotify($this->notifyDefault, $return, $action);
                            } elseif (is_array($return) || $return instanceof jsExpressionable) {
                                return $return;
                            } elseif ($return instanceof Model) {
                                $msg = $m->loaded() ? $this->saveMsg : $this->deleteMsg;
                                return $this->jsRespond($this->getJsNotify($this->notifyDefault, $msg, $action));
                            }
                        };
                        if ($ex instanceof jsUserAction) {
                            $id_arg[0] = (new jQuery())->parents('.atk-card')->data('id');
                        }
                        $action->ui['executor'] = $ex;
                        $c->addClickAction($action, null, array_merge($id_arg, $page_arg));
                    }
                }
            }
        });

        // add no record scope action to menu
        if ($this->useAction && $this->menu && $no_records_actions = $model->getActions(Generic::NO_RECORDS)) {
            foreach ($no_records_actions as $action) {
                $executor = $this->factory($this->getActionExecutor($action));
                $action->ui['executor'] = $executor;
                $executor->addHook('afterExecute', function ($ex, $return, $id) use ($action) {
                    // set action response depending on the return
                    if (is_string($return)) {

                        return  $this->getJsNotify($this->notifyDefault, $return, $action);
                    } elseif (is_array($return) || $return instanceof jsExpressionable) {
                        return $return;
                    } elseif ($return instanceof Model) {
                        $msg = $return->loaded() ? $this->saveMsg : 'Done!';
                        return $this->jsRespond($this->getJsNotify($this->notifyDefault, $msg, $action));
                    }
                });
                $this->addMenuButton($action);
            }
        }

        return $this->model;
    }

    /**
     * Add button to menu bar on top of deck card.
     *
     * @param Button|string|Generic                  $button    A button object, a model action or a string representing a model action.
     * @param null|Generic|jsExpressionable|Callable $callback  An model action, js expression or callback function.
     * @param bool $confirm
     * @param bool $isDisabled
     *
     * @return mixed
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    public function addMenuButton($button, $callback = null, $confirm = false, $isDisabled = false)
    {
        // If action is not specified, perhaps it is defined in the model
        if (!$callback && is_string($button)) {
            $model_action = $this->model->getAction($button);
            if ($model_action) {
                $isDisabled = !$model_action->enabled;
                $callback = $model_action;
                $button = $callback->caption;
                if ($model_action->ui['confirm'] ?? null) {
                    $confirm = $model_action->ui['confirm'];
                }
            }
        } elseif (!$callback && $button instanceof \atk4\data\UserAction\Generic) {
            $isDisabled = !$button->enabled;
            if ($button->ui['confirm'] ?? null) {
                $confirm = $button->ui['confirm'];
            }
            $callback = $button;
            $button = $button->caption;
        }

        if ($callback instanceof \atk4\data\UserAction\Generic) {
            if (isset($callback->ui['button'])) {
                $button = $callback->ui['button'];
            }

            if (isset($callback->ui['confirm'])) {
                $confirm = $callback->ui['confirm'];
            }
        }

        if (!is_object($button)) {
            if (is_string($button)) {
                $button = [$button, 'ui' => 'button '.$this->menuBtnStyle];
            }
            $button = $this->factory('Button', $button, 'atk4\ui');
        }

        if ($button->icon && !is_object($button->icon)) {
            $button->icon = $this->factory('Icon', [$button->icon], 'atk4\ui');
        }

        if ($isDisabled) {
            $button->addClass('disabled');
        }

        $btn = $this->btns->add($button);
        $btn->on('click', $callback, ['confirm' => $confirm]);

        return $btn;
    }

    /**
     * Return proper action executor base on model action.
     *
     * @param $action
     *
     * @throws \atk4\core\Exception
     *
     * @return object
     */
    protected function getActionExecutor($action)
    {
        if (isset($action->ui['executor'])) {
            return $this->factory($action->ui['executor']);
        }

        $executor = (!$action->args && !$action->fields && !$action->preview) ? $this->jsExecutor : $this->executor;

        return $this->factory($executor);
    }

    /**
     * Default js action when saving.
     *
     * @throws \atk4\core\Exception
     *
     * @return array
     */
    public function jsRespond($notifier)
    {
        return [
            $notifier,
            $this->container->jsReload([$this->paginator->name => $this->page]),
        ];
    }

    /**
     * Return jsNotifier object.
     * Override this method for setting notifier based on action or model value.
     *
     * @param array        $notifier_seed Notifier Object seed.
     * @param null|string  $msg           The message to display.
     * @param null|Generic $action        The action short name.
     *
     * @return object
     * @throws \atk4\core\Exception
     */
    public function getJsNotify($notifier_seed, $msg = null, $action = null)
    {
        $notifier =  $this->factory($notifier_seed, null, 'atk4\ui');
        if ($msg) {
            $notifier->setMessage($msg);
        }

        return $notifier;
    }

    public function renderView()
    {
        if ($this->menu) {
            $this->menu->add(['ui' => 'divider']);
        }
        parent::renderView();
    }

    /**
     * Will set model limit according to paginator value.
     *
     * @throws \atk4\data\Exception
     * @throws \atk4\dsql\Exception
     */
    private function _setModelLimitFromPaginator()
    {
        if ($this->paginator) {
            $this->paginator->setTotal(ceil($this->model->action('count')->getOne() / $this->ipp));
            $this->model->setLimit($this->ipp, ($this->paginator->page - 1) * $this->ipp);
        }
    }
}
