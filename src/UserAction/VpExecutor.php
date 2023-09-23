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
class VpExecutor extends View implements JsExecutorInterface
{
    use CommonExecutorTrait;
    use HookTrait;
    use StepExecutorTrait;

    public const HOOK_STEP = self::class . '@onStep';

    /** @var VirtualPage */
    protected $vp;

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

    protected function init(): void
    {
        parent::init();

        $this->initExecutor();
    }

    protected function initExecutor(): void
    {
        $this->vp = VirtualPage::addTo($this);
        /** @var Button $b */
        $b = $this->vp->add(Factory::factory($this->cancelButtonSeed));
        $b->link($this->getApp()->url());
        View::addTo($this->vp, ['ui' => 'clearing divider']);

        $this->header = Header::addTo($this->vp);
        $this->stepList = View::addTo($this->vp)->addClass('ui horizontal bulleted link list');
    }

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
        $this->loader = Loader::addTo($this->vp, ['ui' => $this->loaderUi, 'shim' => $this->loaderShim]);
        $this->actionData = $this->loader->jsGetStoreData()['session'];
    }

    public function setAction(Model\UserAction $action)
    {
        $this->action = $action;
        $this->afterActionInit();

        // get necessary step need prior to execute action
        $this->steps = $this->getSteps();
        if ($this->steps !== []) {
            $this->header->set($this->title ?? $action->getDescription());
            $this->step = $this->stickyGet('step') ?? $this->steps[0];
            $this->vp->add($this->createButtonBar()->setStyle(['text-align' => 'end']));
            $this->addStepList();
        }

        $this->actionInitialized = true;

        return $this;
    }

    public function jsExecute(array $urlArgs = []): JsBlock
    {
        $urlArgs['step'] = $this->step;

        return new JsBlock([(new JsChain('atk.utils'))->redirect($this->vp->getUrl(), $urlArgs)]);
    }

    /**
     * Perform model action by stepping through args - fields - preview.
     */
    public function executeModelAction(): void
    {
        $this->action = $this->executeModelActionLoad($this->action);

        $this->vp->set(function () {
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
            View::addTo($this->stepList)->set($this->stepListItems[$step])->addClass('item')->setAttr(['data-list-item' => $step]);
        }
    }

    protected function jsSetListState(View $view, string $currentStep): void
    {
        $view->js(true, $this->stepList->js()->find('.item')->removeClass('active'));
        foreach ($this->steps as $step) {
            if ($step === $currentStep) {
                $view->js(true, $this->stepList->js()->find('[data-list-item="' . $step . '"]')->addClass('active'));
            }
        }
    }

    /**
     * Return proper JS statement need after action execution.
     *
     * @param mixed      $obj
     * @param string|int $id
     */
    protected function jsGetExecute($obj, $id): JsBlock
    {
        $success = $this->jsSuccess instanceof \Closure
            ? ($this->jsSuccess)($this, $this->action->getModel(), $id, $obj)
            : $this->jsSuccess;

        return new JsBlock([
            JsBlock::fromHookResult($this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$obj, $id]) // @phpstan-ignore-line
                ?: ($success ?? new JsToast('Success' . (is_string($obj) ? (': ' . $obj) : '')))),
            $this->loader->jsClearStoreData(true),
            (new JsChain('atk.utils'))->redirect($this->url()),
        ]);
    }
}
