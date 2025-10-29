<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Exception;
use Atk4\Data\Model;
use Atk4\Data\Reference;

/**
 * TODO move to atk4/data and allow deep/nested changes.
 */
class TheirChanges
{
    /** @var list<array<string, mixed>> */
    public array $inserts = [];

    /** @var list<array{array<string, mixed>, array<string, mixed>}> */
    public array $updates = [];

    /** @var list<array<string, mixed>> */
    public array $deletes = [];

    /**
     * @param array<string, mixed> $oldData
     */
    protected function loadEntity(Model $modelOrEntity, array $oldData): Model
    {
        $entity = $modelOrEntity->isEntity()
            ? $modelOrEntity
            : $modelOrEntity->load($oldData[$modelOrEntity->idField]);

        foreach ($oldData as $k => $v) {
            if (!$entity->compare($k, $v)) {
                throw (new Exception('Field value does not match expected value'))
                    ->addMoreInfo('entity', $entity)
                    ->addMoreInfo('field', $k)
                    ->addMoreInfo('valueExpected', $v)
                    ->addMoreInfo('valueActual', $entity->get($k));
            }
        }

        return $entity;
    }

    public function saveTo(Model $theirModelOrEntity): void
    {
        $theirModelOrEntity->atomic(function () use ($theirModelOrEntity) {
            foreach ($this->deletes as $oldData) {
                $entity = $this->loadEntity($theirModelOrEntity, $oldData);

                $entity->delete();
            }

            foreach ($this->updates as [$oldData, $newData]) {
                $entity = $this->loadEntity($theirModelOrEntity, $oldData);

                $entity->setMulti($newData);
                $entity->save();
            }

            foreach ($this->inserts as $newData) {
                $entity = $theirModelOrEntity->isEntity()
                    ? $theirModelOrEntity
                    : $theirModelOrEntity->createEntity();
                $this->loadEntity($entity, [$theirModelOrEntity->idField => null]);

                if (($newData[$theirModelOrEntity->idField] ?? null) === null) {
                    unset($newData[$theirModelOrEntity->idField]);
                }

                $entity->setMulti($newData);
                $entity->save();
            }
        });
    }

    public function saveOnSave(Model $ourEntity, Reference $theirReference): void
    {
        $ourEntity->assertIsEntity();
        $theirReference->assertOurModelOrEntity($ourEntity);

        $hookIndex = $ourEntity->onHook(Model::HOOK_AFTER_SAVE, function (Model $m) use ($ourEntity, $theirReference, &$hookIndex) {
            assert($m === $ourEntity); // prevent cloning

            $ourEntity->removeHook(Model::HOOK_AFTER_SAVE, $hookIndex, true);

            $theirModelOrEntity = $theirReference->ref($m);

            $this->saveTo($theirModelOrEntity);
        });
    }
}
