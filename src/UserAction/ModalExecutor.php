<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Loader;
use Atk4\Ui\Modal;
use Atk4\Ui\View;

/**
 * Modal executor for action.
 * These are special modal that will divide a model action into steps
 * and run each step accordingly via a loader setup in modal view.
 * The step orders are Argument, Field and Preview, prior to execute the model action.
 *
 * It will first determine the number of step necessary to run the model
 * action. When a step is running through the view loader, data collect for each step
 * are store in browser session storage via javascript. Thus, each request to execute loader,
 * include step data within the request.
 *
 * ModalExecutor modal view may be generated via callbacks.
 * These modal are added to app->html view if not already added
 * and the api service take care of generating them when output
 * in JSON via callback. It is important that these ModalExecutor modals
 * stay within the page HTML content for loader to run each steps properly.
 */
class ModalExecutor extends Modal implements JsExecutorInterface
{
    use CommonExecutorTrait;
    use HookTrait;
    use InnerLoaderTrait;
    use StepExecutorTrait;

    public const HOOK_STEP = self::class . '@onStep';

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->initExecutor();
    }

    protected function initExecutor(): void {}

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
            $this->title ??= $action->getDescription();

            // get current step
            $this->step = $this->stickyGet('step') ?? $this->steps[0];
        }

        $this->actionInitialized = true;

        return $this;
    }

    /**
     * Perform model action by stepping through args - fields - preview.
     */
    #[\Override]
    public function executeModelAction(): void
    {
        $this->action = $this->executeModelActionLoad($this->action);

        // add buttons to modal for next and previous
        $this->addButtonAction($this->createButtonBar());
        $this->jsSetButtonsState($this->loader, $this->step);
        $this->runSteps();
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
        if (!$this->actionInitialized) {
            throw new Exception('Action must be set prior to assign trigger');
        }

        $urlArgs['step'] = $this->step;

        return $this->jsLoadAndShow($urlArgs);
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
            $this->jsHide(),
            JsBlock::fromHookResult($this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$obj, $id]) // @phpstan-ignore ternary.shortNotAllowed
                ?: ($success ?? new JsToast('Success' . (is_string($obj) ? (': ' . $obj) : '')))),
            $this->loader->jsClearStoreData(true),
        ]);
    }
}
