<?php

declare(strict_types=1);

namespace atk4\ui\UserAction;

use atk4\core\HookTrait;
use atk4\data\Model;
use atk4\ui\Exception;
use atk4\ui\Jquery;
use atk4\ui\JsCallback;
use atk4\ui\JsExpressionable;
use atk4\ui\JsToast;
use atk4\ui\View;

/**
 * Javascript Action executor.
 *
 * Will execute a model action using a js Event.
 *
 * Usage:
 * $btn->on('click', new UserAction\JsEventExecutor($btn, $action, $actionArgs));
 *
 * You can add confirmation via the on handler.
 *
 * $btn->on('click', new UserAction\JsEventExecutor($btn, $action, $actionArgs), ['confirm' => 'Sure?']);
 */
class JsEventExecutor implements JsExpressionable
{
    use HookTrait;

    /**
     * @var View The View where Fomantic api context is applied.
     *           It is also the html element that will get the loading css class during callback execution.
     */
    public $context;

    /**
     * The selector for where api call will apply loading context.
     *
     * @var string
     */
    public $stateContext;

    /** @var Model\UserAction The model user action */
    public $action;

    /** @var mixed The model id to load */
    public $modelId;

    /** @var array The action arguments */
    public $args;

    /** @var JsCallback */
    public $cb;

    /**
     * js executable to run after action successfully execute.
     *
     * @var JsExpressionable
     */
    public $jsSuccess;

    public function __construct(View $context = null, Model\UserAction $action = null, $modelId = null, array $args = [], $stateContext = null)
    {
        $this->setContext($context);
        if ($action) {
            $this->setAction($action);
        }
        $this->setModelId($modelId);
        $this->setArgs($args);
        $this->setStateContext($stateContext);
    }

    public function setContext($context)
    {
        $this->context = $context;
        if ($this->context) {
            if (!$context->app) {
                throw new Exception('Context must be part of a render tree. Missing app property.');
            }

            $this->cb = JsCallback::addTo($this->context);
        }
    }

    public function setAction(Model\UserAction $action)
    {
        $this->action = $action;

        if (!$this->action->enabled && $this->context) {
            $this->context->addClass('disabled');
        }
    }

    public function setModelId($id)
    {
        $this->modelId = $id;
    }

    public function setArgs($args)
    {
        $this->args = $args;
    }

    public function setStateContext($stateContext)
    {
        $this->stateContext = $stateContext;
    }

    /**
     * Check if all argument values have been provided.
     */
    public function hasAllArguments()
    {
        $errors = [];
        foreach ($this->action->args as $key => $val) {
            if (!isset($this->args[$key])) {
                $errors[] = $key;
            }
        }

        return $errors;
    }

    public function jsRender(): string
    {
        $this->cb->set(function () {
            $id = $_POST['atk_event_id'] ?? null;

            if ($id && $this->action->appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD) {
                $this->action->owner->tryLoad($id);
            }

            if ($errors = $this->hasAllArguments()) {
                $js = new JsToast(['title' => 'Error', 'message' => 'Missing Arguments: ' . implode(', ', $errors), 'class' => 'error']);
            } else {
                $args = [];
                foreach ($this->action->args as $key => $val) {
                    $args[] = $_POST[$key] ?? $this->args[$key];
                }

                $return = $this->action->execute(...$args);
                $success = $this->jsSuccess instanceof \Closure
                    ? ($this->jsSuccess)($this, $this->action->owner, $id)
                    : $this->jsSuccess;

                $js = $this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$return, $id]) ?: $success ?: new JsToast('Success' . (is_string($return) ? (': ' . $return) : ''));
            }

            return $js;
        });

        $final = (new Jquery($this->context))
            ->atkAjaxec([
                'uri' => $this->cb->getJsUrl(),
                'uri_options' => array_merge(['atk_event_id' => $this->modelId], $this->args),
                'apiConfig' => ['stateContext' => $this->stateContext],
            ]);

        return $final->jsRender();
    }
}
