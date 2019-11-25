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

    /** @var bool Whether card should use table display or not. */
    public $useTable = false;

    /** @var bool Whether card should use label display or not.  */
    public $useLabel = false;

    /** @var null|string If using extra field in Card, glue, join them using extra glue. */
    public $extraGlue = null;

    /** @var bool If each card should use action or not. */
    public $useAction = true;

    /** @var null|View The container view. */
    public $container = null;

    /** @var null|View The paginator view. */
    public $paginator = null;

    /** @var int The number of card to be display per page. */
    public $ipp = 6;

    /** @var null|integer The current page number. */
    private $page = null;

    /** @var string Default executor class. */
    public $executor = UserAction::class;

    /** @var string Default jsExecutor class. */
    public $jsExecutor = jsUserAction::class;

    /** @var array Default notifier to perform when adding or editing is successful * */
    public $notifyDefault = ['jsToast', 'settings' => ['message' => '', 'class' => 'success']];

    public $saveMsg = 'Record has been saved!';
    public $deleteMsg = 'Record has been deleted!';


    public function init()
    {
        parent::init();

        $this->container = $this->add(['defaultTemplate' => 'card-deck.html'])->addClass('ui basic segment');

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
            /** @var  $c Card*/
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
                                $this->setNotifierMsg($return);
                                return  $this->getJsNotify($return);
                            } elseif(is_array($return) || $return instanceof jsExpressionable) {
                                return $return;
                            } elseif ($return instanceof Model) {
                                return $m->loaded() ? $this->jsRespond($this->saveMsg) : $this->jsRespond($this->deleteMsg);
                            }
                        };
                        if ($ex instanceof jsUserAction) {
                            $id_arg[0] =  (new jQuery())->parents('.atk-card')->data('id');
                        }
                        $action->ui['executor'] = $ex;
                        $c->addClickAction($action, null, array_merge($id_arg, $page_arg));
                    }
                }
            }
        });

        return $this->model;
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
    public function jsRespond($msg)
    {
//        $this->setNotifierMsg($msg);
        return [
//            $this->factory($this->notifyDefault, null, 'atk4\ui'),
            $this->getJsNotify($msg),
            $this->container->jsReload([$this->paginator->name => $this->page])
        ];
    }

    public function getJsNotify($msg)
    {
        $this->setNotifierMsg($msg);
        return $this->factory($this->notifyDefault, null, 'atk4\ui');
    }

    /**
     * Set defaultNotifier message.
     *
     * @param $msg
     */
    protected function setNotifierMsg($msg)
    {
        $this->notifyDefault['settings']['message'] = $msg;
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
