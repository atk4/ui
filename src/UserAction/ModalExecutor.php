<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\JsToast;
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
 * in json via callback. It is important that these ModalExecutor modals
 * stay within the page html content for loader to run each steps properly.
 */
class ModalExecutor extends Modal implements JsExecutorInterface
{
    use HookTrait;
    use StepExecutorTrait;

    /** @const string */
    public const HOOK_STEP = self::class . '@onStep';

    protected function init(): void
    {
        parent::init();

        $this->initExecutor();
    }

    protected function initExecutor(): void
    {
        $this->observeChanges();
    }

    public function getAction(): Model\UserAction
    {
        return $this->action;
    }

    /**
     * Make sure modal id is unique.
     * Since User action can be added via callbacks, we need
     * to make sure that view id is properly set for loader and button
     * js action to run properly.
     */
    protected function afterActionInit(Model\UserAction $action): void
    {
        $this->loader = Loader::addTo($this, ['ui' => $this->loaderUi, 'shim' => $this->loaderShim]);
        $this->loader->loadEvent = false;
        $this->loader->addClass('atk-hide-loading-content');
        $this->actionData = $this->loader->jsGetStoreData()['session'];
    }

    public function setAction(Model\UserAction $action)
    {
        $this->action = $action;
        $this->afterActionInit($action);

        // get necessary step need prior to execute action.
        if ($this->steps = $this->getSteps($action)) {
            $this->title ??= $action->getDescription();

            // get current step.
            $this->step = $this->stickyGet('step') ?? $this->steps[0];
        }

        $this->actionInitialized = true;

        return $this;
    }

    /**
     * Perform model action by stepping through args - fields - preview.
     */
    public function executeModelAction(): void
    {
        $id = $this->stickyGet($this->name);
        if ($id && $this->action->appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD) {
            $this->action = $this->action->getActionForEntity($this->action->getModel()->load($id));
        } elseif (!$this->action->isOwnerEntity()
            && in_array($this->action->appliesTo, [Model\UserAction::APPLIES_TO_NO_RECORDS, Model\UserAction::APPLIES_TO_SINGLE_RECORD], true)
        ) {
            $this->action = $this->action->getActionForEntity($this->action->getModel()->createEntity());
        }

        if ($this->action->fields === true) {
            $this->action->fields = array_keys($this->action->getModel()->getFields('editable'));
        }
        // Add buttons to modal for next and previous.
        $this->addButtonAction($this->createButtonBar($this->action));
        $this->jsSetBtnState($this->loader, $this->step);
        $this->runSteps();
    }

    /**
     * Assign a View that will fire action execution.
     * If action require steps, it will automatically initialize
     * proper step to execute first.
     */
    public function assignTrigger(View $view, array $urlArgs = [], string $when = 'click', string $selector = null): self
    {
        if (!$this->actionInitialized) {
            throw new Exception('Action must be set prior to assign trigger');
        }

        if ($this->steps) {
            // use modal for stepping action.
            $urlArgs['step'] = $this->step;
            if ($this->action->enabled) {
                $view->on($when, $selector, [$this->show(), $this->loader->jsLoad($urlArgs, ['method' => 'post'])]);
            } else {
                $view->addClass('disabled');
            }
        }

        return $this;
    }

    /**
     * Generate js for triggering action.
     */
    public function jsExecute(array $urlArgs = []): array
    {
        if (!$this->actionInitialized) {
            throw new Exception('Action must be set prior to assign trigger');
        }

        $urlArgs['step'] = $this->step;

        return [$this->show(), $this->loader->jsLoad($urlArgs, ['method' => 'post'])];
    }

    /**
     * Return proper js statement need after action execution.
     *
     * @param mixed      $obj
     * @param string|int $id
     */
    protected function jsGetExecute($obj, $id): array
    {
        $success = $this->jsSuccess instanceof \Closure
            ? ($this->jsSuccess)($this, $this->action->getModel(), $id, $obj)
            : $this->jsSuccess;

        return [
            $this->hide(),
            $this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$obj, $id]) // @phpstan-ignore-line
                ?: ($success ?? new JsToast('Success' . (is_string($obj) ? (': ' . $obj) : ''))),
            $this->loader->jsClearStoreData(true),
        ];
    }
}
