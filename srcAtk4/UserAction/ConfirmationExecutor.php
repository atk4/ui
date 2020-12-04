<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use atk4\core\HookTrait;
use atk4\data\Model;
use atk4\ui\Button;
use atk4\ui\Exception;
use atk4\ui\JsExpressionable;
use atk4\ui\JsFunction;
use atk4\ui\JsToast;
use atk4\ui\Loader;
use atk4\ui\Modal;
use atk4\ui\Text;
use atk4\ui\View;

/**
 * Modal executor for action that required a confirmation.
 */
class ConfirmationExecutor extends Modal implements JsExecutorInterface
{
    use HookTrait;

    /** @var Model\UserAction|null Action to execute */
    public $action;

    /** @var Loader|null Loader to add content to modal. */
    public $loader;

    /** @var string css class for loader. */
    public $loaderUi = 'ui basic segment';
    /** @var array|View|null Loader shim object or seed. */
    public $loaderShim;
    /** @var JsExpressionable */
    public $jsSuccess;

    /** @var string css class for modal size. */
    public $size = 'tiny';

    /** @var string|null */
    private $step;
    private $actionInitialized = false;

    /** @var Button Ok button */
    private $ok;
    /** @var Button Cancel button */
    private $cancel;

    protected function init(): void
    {
        parent::init();
        $this->observeChanges();
        $this->addClass($this->size);
    }

    /**
     * Properly set element id for this modal.
     */
    public function afterActionInit(Model\UserAction $action)
    {
        $getTableName = function ($arr) {
            foreach ($arr as $k => $v) {
                return is_numeric($k) ? $v : $k;
            }
        };

        $table_name = is_array($action->getModel()->table) ? $getTableName($action->getModel()->table) : $action->getModel()->table;

        $this->id = mb_strtolower($this->name . '_' . $table_name . '_' . $action->short_name);
        $this->name = $this->id;

        // Add buttons to modal for next and previous.
        $btns = (new View())->addStyle(['min-height' => '24px']);
        $this->ok = Button::addTo($btns, ['Ok', 'blue']);
        $this->cancel = Button::addTo($btns, ['Cancel']);
        $this->add($btns, 'actions');
        $this->showActions = true;

        $this->loader = Loader::addTo($this, ['ui' => $this->loaderUi, 'shim' => $this->loaderShim]);
        $this->loader->loadEvent = false;
        $this->loader->addClass('atk-hide-loading-content');
    }

    /**
     * Return js expression that will trigger action executor.
     *
     * @return mixed
     */
    public function jsExecute(array $urlArgs)
    {
        if (!$this->actionInitialized) {
            throw new Exception('Action must be set prior to assign trigger.');
        }

        return [$this->show(), $this->loader->jsLoad($urlArgs, ['method' => 'post'])];
    }

    /**
     * Will associate executor with the action.
     *
     * @return ConfirmationExecutor
     */
    public function setAction(Model\UserAction $action): Modal
    {
        $this->action = $action;
        $this->afterActionInit($action);

        $this->title = $this->title ?? trim($action->caption . ' ' . $this->action->owner->getModelCaption());
        $this->step = $this->stickyGet('step');

        $this->actionInitialized = true;
        $this->jsSetBtnState($this);
        $this->doSteps();

        return $this;
    }

    /**
     * Perform this action steps.
     */
    public function doSteps()
    {
        $id = $this->stickyGet($this->name);
        if ($id && $this->action->appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD) {
            $this->action->owner->tryLoad($id);
        }

        $this->loader->set(function ($modal) {
            $this->jsSetBtnState($modal);
            if ($this->step === 'exec') {
                $this->doFinal($modal);
            } else {
                $this->doConfirmation($modal);
            }
        });
    }

    /**
     * Reset button state.
     */
    protected function jsSetBtnState(View $view)
    {
        // reset button handler.
        $view->js(true, $this->ok->js(true)->off());
        $view->js(true, $this->cancel->js(true)->off());
    }

    /**
     * Set modal for displaying confirmation message.
     * Also apply proper javascript to each button.
     */
    public function doConfirmation(View $modal)
    {
        $this->addConfirmation($modal);

        $modal->js(
            true,
            $this->ok->js()->on(
                'click',
                new JsFunction(
                    [
                        $this->loader->jsload(
                            [
                                'step' => 'exec',
                                $this->name => $this->action->owner->get('id'),
                            ],
                            ['method' => 'post']
                        ),
                    ]
                )
            )
        );

        $modal->js(
            true,
            $this->cancel->js()->on(
                'click',
                new JsFunction(
                    [
                        $this->hide(),
                    ]
                )
            )
        );
    }

    /**
     * Add confirmation message to modal.
     */
    protected function addConfirmation(View $view)
    {
        Text::addTo($view)->set($this->action->getConfirmation());
    }

    /**
     * Execute action when all step are completed.
     */
    protected function doFinal(View $modal)
    {
        $return = $this->action->execute([]);

        $this->_jsSequencer($modal, $this->jsGetExecute($return, $this->action->owner->getId()));
    }

    /**
     * Return proper js statement when action execute.
     *
     * @param $obj
     * @param $id
     */
    protected function jsGetExecute($obj, $id): array
    {
        $success = $this->jsSuccess instanceof \Closure
            ? ($this->jsSuccess)($this, $this->action->owner, $id)
            : $this->jsSuccess;

        return [
            $this->hide(),
            $this->ok->js(true)->off(),
            $this->cancel->js(true)->off(),
            $this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$obj, $id]) ?: $success ?: new JsToast('Success' . (is_string($obj) ? (': ' . $obj) : '')),
        ];
    }

    /**
     * Create a sequence of js statement for a view.
     *
     * @param array|JsExpressionable $js
     */
    private function _jsSequencer(View $view, $js)
    {
        if (is_array($js)) {
            foreach ($js as $jq) {
                $this->_jsSequencer($view, $jq);
            }
        } else {
            $view->js(true, $js);
        }
    }
}
