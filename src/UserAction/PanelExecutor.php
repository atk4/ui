<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Loader;
use Atk4\Ui\Panel\Right;
use Atk4\Ui\View;

/**
 * A Step Action Executor that use a Right Panel.
 */
class PanelExecutor extends Right implements JsExecutorInterface
{
    use CommonExecutorTrait;
    use HookTrait;
    use InnerLoaderTrait;
    use StepExecutorTrait;

    public const HOOK_STEP = self::class . '@onStep';

    /** @var array No need for dynamic content. It is manage with step loader. */
    public $dynamic = [];
    public $hasClickAway = false;

    /** @var string|null */
    public $title;

    /** @var Header */
    public $header;

    /** @var View */
    public $stepList;

    /** @var array<string, string> */
    public $stepListItems = ['args' => 'Fill argument(s)', 'fields' => 'Edit Record(s)', 'preview' => 'Preview', 'final' => 'Complete'];

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->initExecutor();
    }

    protected function initExecutor(): void
    {
        $this->header = Header::addTo($this);
        $this->stepList = View::addTo($this)->addClass('ui horizontal bulleted link list');
    }

    #[\Override]
    public function getAction(): Model\UserAction
    {
        return $this->action;
    }

    /**
     * Make sure modal id is unique.
     * Since User action can be added via callbacks, we need
     * to make sure that view id is properly set for loader and button
     * JS action to run properly.
     */
    protected function afterActionInit(): void
    {
        $this->loader = Loader::addTo($this, ['shim' => $this, 'loadEvent' => false]);
        $this->actionData = $this->loader->jsGetStoreData()['session'];
    }

    #[\Override]
    public function setAction(Model\UserAction $action)
    {
        $this->action = $action;
        $this->afterActionInit();

        // get necessary step need prior to execute action
        $this->steps = $this->getSteps();
        if ($this->steps !== []) {
            $this->header->set($this->title ?? $action->getDescription());
            $this->step = $this->stickyGet('step') ?? $this->steps[0];
            $this->add($this->createButtonBar()->setStyle(['text-align' => 'end']));
            $this->addStepList();
        }

        $this->actionInitialized = true;

        return $this;
    }

    /**
     * @param array<string, string> $urlArgs
     */
    private function jsLoadAndShow(array $urlArgs): JsBlock
    {
        return new JsBlock([
            $this->loader->jsLoad($urlArgs, [
                'onSuccess' => new JsFunction([], [$this->jsOpen()]),
            ]),
        ]);
    }

    #[\Override]
    public function jsExecute(array $urlArgs = []): JsBlock
    {
        $urlArgs['step'] = $this->step;

        return $this->jsLoadAndShow($urlArgs);
    }

    /**
     * Perform model action by stepping through args - fields - preview.
     */
    #[\Override]
    public function executeModelAction(): void
    {
        $this->action = $this->executeModelActionLoad($this->action);

        $this->jsSetButtonsState($this->loader, $this->step);
        $this->jsSetListState($this->loader, $this->step);
        $this->runSteps();
    }

    protected function addStepList(): void
    {
        if (count($this->steps) === 1) {
            return;
        }

        foreach ($this->steps as $step) {
            View::addTo($this->stepList)->set($this->stepListItems[$step])->addClass('item')->setAttr(['data-list-item' => $step]);
        }
    }

    protected function jsSetListState(View $view, string $currentStep): void
    {
        foreach ($this->steps as $step) {
            if ($step === $currentStep) {
                $view->js(true, $this->stepList->js()->find('[data-list-item="' . $step . '"]')->addClass('active'));
            }
        }
    }

    /**
     * Return proper JS statement need after action execution.
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
            $this->jsClose(),
            JsBlock::fromHookResult($this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$obj, $id]) // @phpstan-ignore ternary.shortNotAllowed
                ?: ($success ?? new JsToast('Success' . (is_string($obj) ? (': ' . $obj) : '')))),
            $this->loader->jsClearStoreData(true),
        ]);
    }
}
