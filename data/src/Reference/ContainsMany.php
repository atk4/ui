<?php

declare(strict_types=1);

namespace Atk4\Data\Reference;

use Atk4\Data\Model;
use Atk4\Data\Persistence;

/**
 * ContainsMany reference.
 */
class ContainsMany extends ContainsOne
{
    protected function getDefaultPersistence(Model $theirModel): Persistence
    {
        return new Persistence\Array_([
            $this->table_alias => $this->getOurModel()->isEntity() && $this->getOurFieldValue() !== null ? $this->getOurFieldValue() : [],
        ]);
    }

    /**
     * Returns referenced model.
     */
    public function ref(array $defaults = []): Model
    {
        $ourModel = $this->getOurModel();

        // get model
        $theirModel = $this->createTheirModel(array_merge($defaults, [
            'contained_in_root_model' => $ourModel->contained_in_root_model ?: $ourModel,
            'table' => $this->table_alias,
        ]));

        // set some hooks for ref_model
        foreach ([Model::HOOK_AFTER_SAVE, Model::HOOK_AFTER_DELETE] as $spot) {
            $this->onHookToTheirModel($theirModel, $spot, function ($theirModel) {
                $rows = $theirModel->persistence->getRawDataByTable($theirModel, $this->table_alias);
                $this->getOurModel()->save([
                    $this->getOurFieldName() => $rows ?: null,
                ]);
            });
        }

        return $theirModel;
    }
}
