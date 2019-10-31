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
use atk4\ui\jsCallback;
use atk4\ui\jsExpressionable;
use atk4\ui\jsToast;
use atk4\ui\View;

class jsUserAction extends jsCallback implements Interface_
{
    use HookTrait;

    /** @var @var Generic The model user action */
    public $action;

    /**
     * @var jsExpressionable array|callable jsExpression to return if action was successful, e.g "new jsToast('Thank you')"
     */
    protected $jsSuccess = null;


    public function setAction(Generic $action, $urlArgs = null)
    {
        $this->action = $action;
        if (!$this->action->enabled && $this->owner instanceof View) {
            $this->owner->addClass('disabled');
        }

        $this->set(function ($j, $id = null) {

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
        }, $urlArgs);

        return $this;
    }

    public function setJsSuccess($fx)
    {
        $this->jsSuccess = $fx;

        return $this;
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
}
