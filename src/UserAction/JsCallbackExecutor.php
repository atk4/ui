<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsCallbackLoadableValue;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\JsCallback;

/**
 * Javascript Action executor.
 *
 * Will execute a model action using a JS Event.
 *
 * Usage:
 * When use with View::on method, then JsCallbackExecutor executor is automatically create.
 *  $button->on('click', $model->getUserAction('delete'), [4, 'confirm' => 'This will delete record with ID 4. Are you sure?']);
 *
 * Manual setup.
 * $action = $model->getUserAction('delete')
 * $ex = JsCallbackExecutor::addTo($app)->setAction($action, [4])
 * $button->on('click', $ex, ['confirm' => 'This will delete record with id 4. Are you sure?']);
 */
class JsCallbackExecutor extends JsCallback implements ExecutorInterface
{
    use HookTrait;

    public Model\UserAction $action;

    /** @var JsExpressionable|\Closure<T of Model>($this, T, mixed, mixed): ?JsBlock JS expression to return if action was successful, e.g "new JsToast('Thank you')" */
    public $jsSuccess;

    #[\Override]
    public function getAction(): Model\UserAction
    {
        return $this->action;
    }

    #[\Override]
    public function setAction(Model\UserAction $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @template T
     *
     * @param \Closure(): T                                 $fx
     * @param array<string, JsCallbackLoadableValue|string> $urlArgs
     *
     * @return T
     */
    protected function invokeFxWithUrlArgs(\Closure $fx, array $urlArgs = [])
    {
        $argsOrig = $this->args;
        $this->args = array_merge($this->args, $urlArgs);
        try {
            return $fx();
        } finally {
            $this->args = $argsOrig;
        }
    }

    /**
     * @param array<string, JsCallbackLoadableValue|string> $urlArgs
     */
    #[\Override]
    public function jsExecute(array $urlArgs = []): JsBlock
    {
        return $this->invokeFxWithUrlArgs(function () { // backup/restore $this->args and merge them with $urlArgs
            return parent::jsExecute();
        }, $urlArgs);
    }

    private function executeModelActionLoad(Model\UserAction $action): Model\UserAction
    {
        $model = $action->getModel();

        $id = $this->getApp()->uiPersistence->typecastAttributeLoadField(
            $model->getIdField(),
            $this->getApp()->tryGetRequestPostParam($this->name)
        );

        if ($id && $action->appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD) {
            if ($action->isOwnerEntity() && $action->getEntity()->getId()) {
                $action->getEntity()->setId($id); // assert ID is the same
            } else {
                $action = $action->getActionForEntity($model->load($id));
            }
        } elseif (!$action->isOwnerEntity() && in_array($action->appliesTo, [Model\UserAction::APPLIES_TO_NO_RECORD, Model\UserAction::APPLIES_TO_SINGLE_RECORD], true)) {
            $action = $action->getActionForEntity($model->createEntity());
        }

        return $action;
    }

    /**
     * @param array<string, JsCallbackLoadableValue|string> $urlArgs
     */
    #[\Override]
    public function executeModelAction(array $urlArgs = []): void
    {
        $this->invokeFxWithUrlArgs(function () { // backup/restore $this->args mutated in https://github.com/atk4/ui/blob/8926412a31/src/JsCallback.php#L71
            $actionUrlArgs = array_intersect_key($this->args, $this->action->args);
            if (array_keys($actionUrlArgs) !== array_keys($this->action->args)) {
                throw (new Exception('URL arguments does not match user action arguments'))
                    ->addMoreInfo('actionArgs', array_keys($this->action->args))
                    ->addMoreInfo('urlArgs', array_keys($actionUrlArgs));
            }

            $this->set(function (Jquery $j, ...$values) {
                $this->action = $this->executeModelActionLoad($this->action);

                $return = $this->action->execute(...$values);

                $id = $this->action->getEntity()->getId();

                $jsSuccess = $this->jsSuccess instanceof \Closure
                    ? ($this->jsSuccess)($this, $this->action->getModel(), $id, $return)
                    : $this->jsSuccess;

                $js = JsBlock::fromHookResult($this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$return, $id]) // @phpstan-ignore ternary.shortNotAllowed
                    ?: ($jsSuccess ?? new JsToast('Success' . (is_string($return) ? (': ' . $return) : ''))));

                return $js;
            }, $actionUrlArgs);
        }, $urlArgs);
    }
}
