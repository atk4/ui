<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Data\Model;

trait CommonExecutorTrait
{
    protected function executeModelActionLoad(Model\UserAction $action): Model\UserAction
    {
        $model = $action->getModel();

        $id = $this->getApp()->uiPersistence->typecastLoadField(
            $model->getField($model->idField),
            $this->stickyGet($this->name)
        );

        if ($id && $action->appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD) {
            if ($action->isOwnerEntity() && $action->getEntity()->getId()) {
                $action->getEntity()->setId($id); // assert ID is the same
            } else {
                $action = $action->getActionForEntity($model->load($id));
            }
        } elseif (!$action->isOwnerEntity() && in_array($action->appliesTo, [Model\UserAction::APPLIES_TO_NO_RECORDS, Model\UserAction::APPLIES_TO_SINGLE_RECORD], true)) {
            $action = $action->getActionForEntity($model->createEntity());
        }

        if ($action->fields === true) {
            $action->fields = array_keys($model->getFields('editable'));
        }

        return $action;
    }
}
