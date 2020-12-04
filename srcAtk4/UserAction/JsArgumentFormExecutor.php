<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use atk4\core\HookTrait;
use atk4\data\Model;
use atk4\ui\JsExpression;
use atk4\ui\JsModal;
use atk4\ui\JsToast;

/**
 * A js action executor that require a form.
 */
class JsArgumentFormExecutor extends JsModal
{
    use HookTrait;

    /** @const string not used, make it public if needed or drop it */
    private const HOOK_FORM_INIT = self::class . '@formInit';

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

            if ($id && $this->action->appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD) {
                $this->action->owner->tryLoad($id);
            }

            // TODO How do we know if argument is need over model field in action?
            $form->setModel($this->action->owner);

            $form->hook(self::HOOK_FORM_INIT);

            $form->onSubmit(function (\atk4\ui\Form $form) {
                $this->action->fields = array_keys($form->model->getFields('editable'));
                $return = $this->action->execute();

                $js = [
                    new JsExpression('$(".atk-dialog-content").trigger("close")'),
                    $this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$return]) ?: new JsToast('Success' . (is_string($return) ? (': ' . $return) : '')),
                ];

                return $js;
            });
        });
    }
}
