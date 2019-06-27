<?php
/**
 * Javascript Action executor.
 *
 * Will execute a model action using a js Event.
 *
 * Usage:
 * $btn->on('click', new jsEvent($btn, $action, $actionArgs));
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

    /** @var @var Generic The model user action */
    public $action;

    /** @var null The model id to load */
    public $modelId;

    /** @var array The action arguments */
    public $args;

    /** @var jsCallback */
    public $cb;

    /** @var string|null The confirmation message. */
    public $confirm = null;

    public function __construct(View $context, Generic $action, $modelId = null, array $args = [])
    {
        $this->context = $context;
        $this->action = $action;
        $this->modelId = $modelId;
        $this->args = $args;

        if (!$this->context->app) {
            throw new Exception('Context must be part of a render tree. Missing app property.');
        }

        if (!$this->action->enabled) {
            $this->context->addClass('disabled');
        }

        $this->cb = $this->context->add('jsCallback');
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

    /**
     * Ask for confirmation prior to add an action.
     *
     * @param string $text
     *
     * @return $this
     */
    public function setConfirm($text = 'Are you sure')
    {
        $this->confirm = $text;

        return $this;
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

                $js = $this->hook('afterExecute', [$return]) ?: new jsToast('Success'.(is_string($return) ? (': '.$return) : ''));
            }

            return $js;
        });

        $final = (new jQuery($this->context))
            ->atkAjaxec([
                'uri'           => $this->cb->getJSURL(),
                'uri_options'   => array_merge(['atk_event_id' => $this->modelId], $this->args),
                'confirm'       => $this->confirm,
        ]);

        return $final->jsRender();
    }
}
