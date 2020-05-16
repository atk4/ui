<?php
/**
 * A js action executor that require a form.
 */

namespace atk4\ui\ActionExecutor;

use atk4\core\HookTrait;
use atk4\ui\jsExpression;
use atk4\ui\jsModal;
use atk4\ui\jsToast;

class jsArgumentForm extends jsModal
{
    use HookTrait;

    public $vp;
    public $action;
    public $form;

    public function __construct($action, $page, $modelId = null, $form = null)
    {
        $this->vp = $page;
        $this->action = $action;
        $this->form = $form;

        parent::__construct($action->caption, $page, ['atk_event_id' => $modelId]);

        $this->initVp();
    }

    public function initVp()
    {
        $this->vp->set(function ($p) {
            $form = null;
            if (!is_object($this->form) || !$this->form->_initialized) {
                $form = $p->add($this->form ?: 'Form');
            }

            $id = $p->stickyGet('atk_event_id');

            if ($id && $this->action->scope === 'single') {
                $this->action->owner->tryLoad($id);
            }

            // TODO How do we know if argument is need over model field in action?
            $form->setModel($this->action->owner);

            $form->hook('formInit');

            $form->onSubmit(function (\atk4\ui\Form $form) {
                $this->action->fields = array_keys($form->model->getFields('editable'));
                $return = $this->action->execute();

                $js = [
                    new jsExpression('$(".atk-dialog-content").trigger("close")'),
                    $this->hook('afterExecute', [$return]) ?: new jsToast('Success' . (is_string($return) ? (': ' . $return) : '')),
                ];

                return $js;
            });
        });
    }
}
