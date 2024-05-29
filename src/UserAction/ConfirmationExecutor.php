<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Data\Model\UserAction;
use Atk4\Ui\Button;
use Atk4\Ui\Exception;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\Js\JsToast;
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
    use InnerLoaderTrait;

    /** @var UserAction|null Action to execute */
    public $action;

    /** @var JsExpressionable|\Closure<T of Model>($this, T, mixed, mixed): ?JsBlock JS expression to return if action was successful, e.g "new JsToast('Thank you')" */
    public $jsSuccess;

    /** @var string CSS class for modal size. */
    public $size = 'tiny';

    /** @var string|null */
    private $step;

    /** @var Button Ok button */
    private $ok;
    /** @var Button Cancel button */
    private $cancel;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->addClass($this->size);
    }

    /**
     * Properly set element ID for this modal.
     */
    protected function afterActionInit(): void
    {
        // add buttons to modal for next and previous
        $buttonsView = (new View())->setStyle(['min-height' => '24px']);
        $this->ok = Button::addTo($buttonsView, ['Ok', 'class.blue' => true]);
        $this->cancel = Button::addTo($buttonsView, ['Cancel']);
        $this->add($buttonsView, 'actions');
        $this->showActions = true;

        $this->loader = Loader::addTo($this, ['shim' => $this, 'loadEvent' => false]);
    }

    /**
     * @param array<string, string> $urlArgs
     */
    private function jsLoadAndShow(array $urlArgs): JsBlock
    {
        return new JsBlock([
            $this->loader->jsLoad($urlArgs, [
                'method' => 'POST',
                'onSuccess' => new JsFunction([], [$this->jsShow()]),
            ]),
        ]);
    }

    #[\Override]
    public function jsExecute(array $urlArgs = []): JsBlock
    {
        if (!$this->action) {
            throw new Exception('Action must be set prior to assign trigger');
        }

        return $this->jsLoadAndShow($urlArgs);
    }

    #[\Override]
    public function getAction(): UserAction
    {
        return $this->action;
    }

    #[\Override]
    public function setAction(UserAction $action)
    {
        $this->action = $action;
        $this->afterActionInit();

        $this->title ??= $action->getDescription();
        $this->step = $this->stickyGet('step');

        return $this;
    }

    /**
     * Perform the current step.
     */
    #[\Override]
    public function executeModelAction(): void
    {
        $this->action = $this->executeModelActionLoad($this->action);

        $this->loader->set(function (Loader $p) {
            if ($this->step === 'execute') {
                $this->doFinal($p);
            } else {
                $this->doConfirmation($p);
            }
        });
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
            $this->ok->js()->on('click', new JsFunction([], [
                $this->loader->jsLoad(
                    [
                        'step' => 'execute',
                        $this->name => $this->getApp()->uiPersistence->typecastAttributeSaveField($this->action->getModel()->getIdField(), $this->action->getEntity()->getId()),
                    ],
                    ['method' => 'POST']
                ),
            ]))
        );

        $modal->js(
            true,
            $this->cancel->js()->on('click', new JsFunction([], [
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

        $modal->js(true, $this->jsGetExecute($return, $this->action->getEntity()->getId()));
    }

    /**
     * Return proper JS statement when action execute.
     *
     * @param mixed $obj
     * @param mixed $id
     */
    protected function jsGetExecute($obj, $id): JsBlock
    {
        $success = $this->jsSuccess instanceof \Closure
            ? ($this->jsSuccess)($this, $this->action->getModel(), $id, $obj)
            : $this->jsSuccess;

        return new JsBlock([
            $this->jsHide(),
            JsBlock::fromHookResult($this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$obj, $id]) // @phpstan-ignore ternary.shortNotAllowed
                ?: ($success ?? new JsToast('Success' . (is_string($obj) ? (': ' . $obj) : '')))),
        ]);
    }
}
