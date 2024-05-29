<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\Factory;
use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Loader;
use Atk4\Ui\View;
use Atk4\Ui\VirtualPage;

/**
 * A Step Action Executor that use a VirtualPage.
 */
class VpExecutor extends VirtualPage implements JsExecutorInterface
{
    use CommonExecutorTrait;
    use HookTrait;
    use InnerLoaderTrait;
    use StepExecutorTrait;

    public const HOOK_STEP = self::class . '@onStep';

    public $ui = 'container basic fitted segment';

    /** @var string|null */
    public $title;

    /** @var Header */
    public $header;

    /** @var View */
    public $stepList;

    /** @var array<string, string> */
    public $stepListItems = ['args' => 'Fill argument(s)', 'fields' => 'Edit Record(s)', 'preview' => 'Preview', 'final' => 'Complete'];

    /** @var array */
    public $cancelButtonSeed = [Button::class, ['Cancel', 'class.small left floated basic blue' => true, 'icon' => 'left arrow']];

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->initExecutor();
    }

    protected function initExecutor(): void
    {
        /** @var Button $b */
        $b = $this->add(Factory::factory($this->cancelButtonSeed));
        $b->link($this->getApp()->url());
        View::addTo($this, ['ui' => 'clearing divider']);

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

        if ($this->cb->canTerminate()) {
            $this->js(true, $this->loader->jsLoad([
                $this->name => $this->getApp()->getRequestQueryParam($this->name),
            ]));
        }
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

    #[\Override]
    public function jsExecute(array $urlArgs = []): JsBlock
    {
        $urlArgs['step'] = $this->step;

        return new JsBlock([(new JsChain('atk.utils'))->redirect($this->getUrl(), $urlArgs)]);
    }

    /**
     * Perform model action by stepping through args - fields - preview.
     */
    #[\Override]
    public function executeModelAction(): void
    {
        $this->action = $this->executeModelActionLoad($this->action);

        $this->set(function () {
            $this->jsSetButtonsState($this->loader, $this->step);
            $this->jsSetListState($this->loader, $this->step);
            $this->runSteps();
        });
    }

    protected function addStepList(): void
    {
        if (count($this->steps) === 1) {
            return;
        }

        foreach ($this->steps as $step) {
            // TODO replace `(View::class)` with `View` once https://github.com/phpstan/phpstan/issues/10469 is fixed
            (View::class)::addTo($this->stepList)->set($this->stepListItems[$step])->addClass('item')->setAttr(['data-list-item' => $step]);
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
            JsBlock::fromHookResult($this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$obj, $id]) // @phpstan-ignore ternary.shortNotAllowed
                ?: ($success ?? new JsToast('Success' . (is_string($obj) ? (': ' . $obj) : '')))),
            $this->loader->jsClearStoreData(true),
            (new JsChain('atk.utils'))->redirect($this->getOwner()->url()),
        ]);
    }
}
