<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Array_;

use Atk4\Data\Exception;
use Atk4\Data\Model;
use Atk4\Data\Persistence;

/**
 * @property Persistence\Array_|null $persistence
 */
class Join extends Model\Join
{
    /**
     * This method is to figure out stuff.
     */
    protected function init(): void
    {
        parent::init();

        // If kind is not specified, figure out join type
        if (!isset($this->kind)) {
            $this->kind = $this->weak ? 'left' : 'inner';
        }

        // Add necessary hooks
        if ($this->reverse) {
            $this->onHookShortToOwner(Model::HOOK_AFTER_INSERT, \Closure::fromCallable([$this, 'afterInsert']), [], -5);
            $this->onHookShortToOwner(Model::HOOK_BEFORE_UPDATE, \Closure::fromCallable([$this, 'beforeUpdate']), [], -5);
            $this->onHookShortToOwner(Model::HOOK_BEFORE_DELETE, \Closure::fromCallable([$this, 'doDelete']), [], -5);
        } else {
            $this->onHookShortToOwner(Model::HOOK_BEFORE_INSERT, \Closure::fromCallable([$this, 'beforeInsert']));
            $this->onHookShortToOwner(Model::HOOK_BEFORE_UPDATE, \Closure::fromCallable([$this, 'beforeUpdate']));
            $this->onHookShortToOwner(Model::HOOK_AFTER_DELETE, \Closure::fromCallable([$this, 'doDelete']));
            $this->onHookShortToOwner(Model::HOOK_AFTER_LOAD, \Closure::fromCallable([$this, 'afterLoad']));
        }
    }

    protected function makeFakeModelWithForeignTable(): Model
    {
        $modelCloned = clone $this->getOwner();
        $modelCloned->table = $this->foreign_table;

        // @TODO hooks will be fixed on a cloned model, Join should be replaced later by supporting unioned table as a table model

        return $modelCloned;
    }

    /**
     * Called from afterLoad hook.
     */
    public function afterLoad(): void
    {
        $model = $this->getOwner();

        // we need to collect ID
        $this->id = $model->getDataRef()[$this->master_field];
        if (!$this->id) {
            return;
        }

        try {
            $data = Persistence\Array_::assertInstanceOf($model->persistence)
                ->load($this->makeFakeModelWithForeignTable(), $this->id, $this->foreign_table);
        } catch (Exception $e) {
            throw (new Exception('Unable to load joined record', $e->getCode(), $e))
                ->addMoreInfo('table', $this->foreign_table)
                ->addMoreInfo('id', $this->id);
        }
        $dataRef = &$model->getDataRef();
        $dataRef = array_merge($data, $model->getDataRef());
    }

    /**
     * Called from beforeInsert hook.
     */
    public function beforeInsert(array &$data): void
    {
        if ($this->weak) {
            return;
        }

        if ($this->getOwner()->hasField($this->master_field) && $this->getOwner()->get($this->master_field)) {
            // The value for the master_field is set,
            // we are going to use existing record.
            return;
        }

        // Figure out where are we going to save data
        $persistence = $this->persistence ?: $this->getOwner()->persistence;

        $this->id = $persistence->insert(
            $this->makeFakeModelWithForeignTable(),
            $this->save_buffer
        );

        $data[$this->master_field] = $this->id;

        //$this->getOwner()->set($this->master_field, $this->id);
    }

    /**
     * Called from afterInsert hook.
     *
     * @param mixed $id
     */
    public function afterInsert($id): void
    {
        if ($this->weak) {
            return;
        }

        $this->save_buffer[$this->foreign_field] = $this->hasJoin() ? $this->getJoin()->id : $id;

        $persistence = $this->persistence ?: $this->getOwner()->persistence;

        $this->id = $persistence->insert(
            $this->makeFakeModelWithForeignTable(),
            $this->save_buffer
        );
    }

    /**
     * Called from beforeUpdate hook.
     */
    public function beforeUpdate(array &$data): void
    {
        if ($this->weak) {
            return;
        }

        $persistence = $this->persistence ?: $this->getOwner()->persistence;

        $this->id = $persistence->update(
            $this->makeFakeModelWithForeignTable(),
            $this->id,
            $this->save_buffer,
            $this->foreign_table
        );
    }

    /**
     * Called from beforeDelete and afterDelete hooks.
     *
     * @param mixed $id
     */
    public function doDelete($id): void
    {
        if ($this->weak) {
            return;
        }

        $persistence = $this->persistence ?: $this->getOwner()->persistence;

        $persistence->delete(
            $this->makeFakeModelWithForeignTable(),
            $this->id
        );

        $this->id = null;
    }
}
