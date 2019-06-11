<?php
/**
 * A model action that get execute when the target object received an event.
 *
 * Note: Target object is not add to the render tree by default.
 * It needs to be add to the render tree for callback action to work.
 *
 * ex:
 * $executor = new Event(['target' => new Button(['click me'], 'modelId' => $id]);
 * $executor->setAction($modelAction);
 * $app->add($executor->target);
 */

namespace atk4\ui\ActionExecutor;

use atk4\ui\jsToast;
use atk4\ui\View;

class Event extends Basic
{
    /** @var null|View The target object where event is assign. */
    public $target = null;

    /** @var string The event assign to target. */
    public $event = 'click';

    /** @var null The model id to load when target object event is trigger. */
    public $modelId = null;

    /** @var string The css class name to disable the target object. */
    public $disabled = 'disabled';

    /**
     * Set modelId.
     *
     * @param $id
     */
    public function setModelId($id)
    {
        $this->modelId = $id;
    }

    public function recursiveRender()
    {
        if (!$this->action) {
            throw new \atk4\ui\Exception(['Action is not set. Use setAction()']);
        }

        $this->initPreview();
        // check if action can be called
        if (!$this->action->enabled) {
            $this->target->addClass($this->disabled);
        }

        View::recursiveRender();
    }

    protected function initPreview()
    {
        $this->target->on($this->event, null, function () {
            return $this->jsExecute();
        }, ['args' => ['atk_event_id' => $this->modelId]]);
    }

    /**
     * Will call $action->execute() with the correct arguments.
     */
    public function jsExecute()
    {
        $args = [];

        foreach ($this->action->args as $key => $val) {
            $args[] = $this->arguments[$key];
        }

        $id = isset($_POST['atk_event_id']) ? $_POST['atk_event_id'] : null;

        if ($id && $this->hasAllArguments()) {
            $this->action->owner->load($id);
            $return = $this->action->execute(...$args);

            return $this->hook('afterExecute', [$return]) ?: $this->jsSuccess ?: new jsToast('Success'.(is_string($return) ? (': '.$return) : ''));
        } else {
            $error = '';
            if (!$id) {
                $error .= 'No model id was provided.';
            }
            if (!$this->hasAllArguments()) {
                $error .= 'Insufficient arguments';
            }

            return new jsToast([
                                   'title'   => 'Error',
                                   'message' => $error,
                                   'class'   => 'error',
                               ]);
        }
    }
}
