<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Data\Model\UserAction;
use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\JsExpressionable;
use Atk4\Ui\JsFunction;
use Atk4\Ui\JsToast;
use Atk4\Ui\Loader;
use Atk4\Ui\Modal;
use Atk4\Ui\Text;
use Atk4\Ui\View;

/**
 * Modal executor for action that required a confirmation.
 */
class ConfirmationExecutor extends Modal implements JsExecutorInterface
{
    use CommonExecutorTrait;
    use HookTrait;

    /** @var Model\UserAction|null Action to execute */
    public $action;

    /** @var Loader|null Loader to add content to modal. */
    public $loader;

    /** @var string css class for loader. */
    public $loaderUi = 'ui basic segment';
    /** @var array|View|null Loader shim object or seed. */
    public $loaderShim;
    /** @var JsExpressionable|\Closure JsExpression to return if action was successful, e.g "new JsToast('Thank you')" */
    public $jsSuccess;

    /** @var string css class for modal size. */
    public $size = 'tiny';

    /** @var string|null */
    private $step;

    /** @var Button Ok button */
    private $ok;
    /** @var Button Cancel button */
    private $cancel;

    protected function init(): void
    {
        parent::init();

        $this->addClass($this->size);
    }

    /**
     * Properly set element id for this modal.
     */
    protected function afterActionInit(Model\UserAction $action): void
    {
        // Add buttons to modal for next and previous.
        $btns = (new View())->addStyle(['min-height' => '24px']);
        $this->ok = Button::addTo($btns, ['Ok', 'class.blue' => true]);
        $this->cancel = Button::addTo($btns, ['Cancel']);
        $this->add($btns, 'actions');
        $this->showActions = true;

        $this->loader = Loader::addTo($this, ['ui' => $this->loaderUi, 'shim' => $this->loaderShim]);
        $this->loader->loadEvent = false;
        $this->loader->addClass('atk-hide-loading-content');
    }

    /**
     * Return js expression that will trigger action executor.
     */
    public function jsExecute(array $urlArgs = []): array
    {
        if (!$this->action) {
            throw new Exception('Action must be set prior to assign trigger');
        }

        return [$this->jsShow(), $this->loader->jsLoad($urlArgs, ['method' => 'post'])];
    }

    public function getAction(): UserAction
    {
        return $this->action;
    }

    public function setAction(Model\UserAction $action)
    {
        $this->action = $action;
        $this->afterActionInit($action);

        $this->title ??= $action->getDescription();
        $this->step = $this->stickyGet('step');

        $this->jsSetBtnState($this);

        return $this;
    }

    /**
     * Perform this action steps.
     */
    public function executeModelAction(): void
    {
        $this->action = $this->executeModelActionLoad($this->action);

        $this->loader->set(function (Loader $p) {
            $this->jsSetBtnState($p);
            if ($this->step === 'exec') {
                $this->doFinal($p);
            } else {
                $this->doConfirmation($p);
            }
        });
    }

    /**
     * Reset button state.
     */
    protected function jsSetBtnState(View $view): void
    {
        $view->js(true, $this->ok->js(true)->off());
        $view->js(true, $this->cancel->js(true)->off());
    }

    /**
     * Set modal for displaying confirmation message.
     * Also apply proper javascript to each button.
     */
    public function doConfirmation(View $modal): void
    {
        $this->addConfirmation($modal);

        $modal->js(
            true,
            $this->ok->js()->on('click', new JsFunction([
                $this->loader->jsLoad(
                    [
                        'step' => 'exec',
                        $this->name => $this->action->getEntity()->getId(),
                    ],
                    ['method' => 'post']
                ),
            ]))
        );

        $modal->js(
            true,
            $this->cancel->js()->on('click', new JsFunction([
                $this->jsHide(),
            ]))
        );
    }

    /**
     * Add confirmation message to modal.
     */
    protected function addConfirmation(View $view): void
    {
        Text::addTo($view)->set($this->action->getConfirmation());
    }

    /**
     * Execute action when all step are completed.
     */
    protected function doFinal(View $modal): void
    {
        $return = $this->action->execute([]);

        $this->_jsSequencer($modal, $this->jsGetExecute($return, $this->action->getEntity()->getId()));
    }

    /**
     * Return proper js statement when action execute.
     *
     * @param mixed      $obj
     * @param string|int $id
     */
    protected function jsGetExecute($obj, $id): array
    {
        $success = $this->jsSuccess instanceof \Closure
            ? ($this->jsSuccess)($this, $this->action->getModel(), $id)
            : $this->jsSuccess;

        return [
            $this->jsHide(),
            $this->ok->js(true)->off(),
            $this->cancel->js(true)->off(),
            $this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$obj, $id]) // @phpstan-ignore-line
                ?: ($success ?? new JsToast('Success' . (is_string($obj) ? (': ' . $obj) : ''))),
        ];
    }

    /**
     * Create a sequence of js statement for a view.
     *
     * @param array|JsExpressionable $js
     */
    private function _jsSequencer(View $view, $js): void
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
