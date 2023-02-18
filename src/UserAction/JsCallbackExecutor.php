<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\JsCallback;
use Atk4\Ui\View;

/**
 * Javascript Action executor.
 *
 * Will execute a model action using a js Event.
 *
 * Usage:
 * When use with View::on method, then JsCallbackExecutor executor is automatically create.
 *  $btn->on('click', $model->getUserAction('delete'), [4, 'confirm' => 'This will delete record with id 4. Are you sure?']);
 *
 * Manual setup.
 * $action = $model->getUserAction('delete')
 * $ex = JsCallbackExecutor::addTo($app)->setAction($action, [4])
 * $btn->on('click', $ex, ['confirm' => 'This will delete record with id 4. Are you sure?']);
 */
class JsCallbackExecutor extends JsCallback implements ExecutorInterface
{
    use HookTrait;

    /** @var Model\UserAction The model user action */
    public $action;

    /** @var JsExpressionable|\Closure JS expression to return if action was successful, e.g "new JsToast('Thank you')" */
    public $jsSuccess;

    public function getAction(): Model\UserAction
    {
        return $this->action;
    }

    public function setAction(Model\UserAction $action)
    {
        $this->action = $action;
        if (!$this->action->enabled && $this->getOwner() instanceof View) { // @phpstan-ignore-line
            $this->getOwner()->addClass('disabled');
        }

        return $this;
    }

    public function jsExecute(array $urlArgs = []): JsExpression
    {
        // TODO hack to parametrize parent::jsExecute() like JsExecutorInterface::jsExecute($urlArgs)
        $argsOrig = $this->args;
        $this->args = array_merge($this->args, $urlArgs);
        try {
            return parent::jsExecute();
        } finally {
            $this->args = $argsOrig;
        }
    }

    public function executeModelAction(array $args = []): void
    {
        $this->set(function (Jquery $j) {
            $id = $this->getApp()->uiPersistence->typecastLoadField(
                $this->action->getModel()->getField($this->action->getModel()->idField),
                $_POST['c0'] ?? $_POST[$this->name] ?? null
            );
            if ($id && $this->action->appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD) {
                if ($this->action->isOwnerEntity() && $this->action->getEntity()->getId()) {
                    $this->action->getEntity()->setId($id); // assert ID is the same
                } else {
                    $this->action = $this->action->getActionForEntity($this->action->getModel()->load($id));
                }
            } elseif (!$this->action->isOwnerEntity()
                && in_array($this->action->appliesTo, [Model\UserAction::APPLIES_TO_NO_RECORDS, Model\UserAction::APPLIES_TO_SINGLE_RECORD], true)
            ) {
                $this->action = $this->action->getActionForEntity($this->action->getModel()->createEntity());
            }

            $errors = $this->_hasAllArguments();
            if ($errors) {
                $js = new JsToast(['title' => 'Error', 'message' => 'Missing Arguments: ' . implode(', ', $errors), 'class' => 'error']);
            } else {
                $args = [];
                foreach ($this->action->args as $key => $val) {
                    $args[] = $_POST[$key] ?? $this->args[$key];
                }

                $return = $this->action->execute(...$args);
                $success = $this->jsSuccess instanceof \Closure
                    ? ($this->jsSuccess)($this, $this->action->getModel(), $id, $return)
                    : $this->jsSuccess;

                $js = $this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$return, $id]) // @phpstan-ignore-line
                    ?: ($success ?? new JsToast('Success' . (is_string($return) ? (': ' . $return) : '')));
            }

            return $js;
        }, $args);
    }

    /**
     * Check if all argument values have been provided.
     */
    private function _hasAllArguments(): array
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
