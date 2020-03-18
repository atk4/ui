<?php
/**
 * Javascript Action executor.
 *
 * Will execute a model action using a js Event.
 *
 * Usage:
 * $btn->on('click', new jsEvent($btn, $action, $actionArgs));
 *
 * You can add confirmation via the on handler.
 *
 * $btn->on('click', new jsEvent($btn, $action, $actionArgs), ['confirm' => 'Sure?']);
 */

namespace atk4\ui\ActionExecutor;

use atk4\core\HookTrait;
use atk4\data\UserAction\Generic;
use atk4\ui\Exception;
use atk4\ui\jQuery;
use atk4\ui\jsCallback;
use atk4\ui\jsExpressionable;
use atk4\ui\jsToast;
use atk4\ui\View;

class jsEvent implements jsExpressionable
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

    /** @var @var Generic The model user action */
    public $action;

    /** @var null The model id to load */
    public $modelId;

    /** @var array The action arguments */
    public $args;

    /** @var jsCallback */
    public $cb;

    /**
     * js executable to run after action successfully execute.
     *
     * @var jsExpressionable
     */
    public $jsSuccess = null;

    public function __construct(View $context = null, Generic $action = null, $modelId = null, array $args = [], $stateContext = null)
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

            $this->cb = $this->context->add('jsCallback');
        }
    }

    public function setAction(Generic $action)
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

    public function jsRender()
    {
        $this->cb->set(function () {
            $id = $_POST['atk_event_id'] ?? null;

            if ($id && $this->action->scope === 'single') {
                $this->action->owner->tryLoad($id);
            }

            if ($errors = $this->hasAllArguments()) {
                $js = new jsToast(['title' => 'Error', 'message' => 'Missing Arguments: '.implode(', ', $errors), 'class' => 'error']);
            } else {
                $args = [];
                foreach ($this->action->args as $key => $val) {
                    $args[] = $_POST[$key] ?? $this->args[$key];
                }

                $return = $this->action->execute(...$args);
                $success = is_callable($this->jsSuccess) ? call_user_func_array($this->jsSuccess, [$this, $this->action->owner, $id]) : $this->jsSuccess;

                $js = $this->hook('afterExecute', [$return, $id]) ?: $success ?: new jsToast('Success'.(is_string($return) ? (': '.$return) : ''));
            }

            return $js;
        });

        $final = (new jQuery($this->context))
            ->atkAjaxec([
                'uri'           => $this->cb->getJSURL(),
                'uri_options'   => array_merge(['atk_event_id' => $this->modelId], $this->args),
                'apiConfig'     => ['stateContext' => $this->stateContext],
            ]);

        return $final->jsRender();
    }
}
