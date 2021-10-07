<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql;

use Atk4\Data\Model;
use Atk4\Data\Persistence;

/**
 * @property Persistence\Sql $persistence
 */
class Join extends Model\Join implements \Atk4\Data\Persistence\Sql\Expressionable
{
    /**
     * By default we create ON expression ourselves, but if you want to specify
     * it, use the 'on' property.
     *
     * @var \Atk4\Data\Persistence\Sql\Expression|string|null
     */
    protected $on;

    /**
     * Will use either foreign_alias or create #join_<table>.
     */
    public function getDesiredName(): string
    {
        return '_' . ($this->foreign_alias ?: $this->foreign_table[0]);
    }

    public function getDsqlExpression(Expression $expr): Expression
    {
        /*
        // If our Model has expr() method (inherited from Persistence\Sql) then use it
        if ($this->getOwner()->hasMethod('expr')) {
            return $this->getOwner()->expr('{}.{}', [$this->foreign_alias, $this->foreign_field]);
        }

        // Otherwise call it from expression itself
        return $expr->expr('{}.{}', [$this->foreign_alias, $this->foreign_field]);
        */

        // Romans: Join\Sql shouldn't even be called if expr is undefined. I think we should leave it here to produce error.
        return $this->getOwner()->expr('{}.{}', [$this->foreign_alias, $this->foreign_field]);
    }

    /**
     * This method is to figure out stuff.
     */
    protected function init(): void
    {
        parent::init();

        $this->getOwner()->persistence_data['use_table_prefixes'] = true;

        // If kind is not specified, figure out join type
        if (!isset($this->kind)) {
            $this->kind = $this->weak ? 'left' : 'inner';
        }

        // Our short name will be unique
        if (!$this->foreign_alias) {
            $this->foreign_alias = ($this->getOwner()->table_alias ?: '') . $this->short_name;
        }

        $this->onHookShortToOwner(Persistence\Sql::HOOK_INIT_SELECT_QUERY, \Closure::fromCallable([$this, 'initSelectQuery']));

        // Add necessary hooks
        if ($this->reverse) {
            $this->onHookShortToOwner(Model::HOOK_AFTER_INSERT, \Closure::fromCallable([$this, 'afterInsert']));
            $this->onHookShortToOwner(Model::HOOK_BEFORE_UPDATE, \Closure::fromCallable([$this, 'beforeUpdate']));
            $this->onHookShortToOwner(Model::HOOK_BEFORE_DELETE, \Closure::fromCallable([$this, 'doDelete']), [], -5);
            $this->onHookShortToOwner(Model::HOOK_AFTER_LOAD, \Closure::fromCallable([$this, 'afterLoad']));
        } else {
            // Master field indicates ID of the joined item. In the past it had to be
            // defined as a physical field in the main table. Now it is a model field
            // so you can use expressions or fields inside joined entities.
            // If string specified here does not point to an existing model field
            // a new basic field is inserted and marked hidden.
            if (!$this->getOwner()->hasField($this->master_field)) {
                $owner = $this->hasJoin() ? $this->getJoin() : $this->getOwner();

                $field = $owner->addField($this->master_field, ['system' => true, 'read_only' => true]);

                $this->master_field = $field->short_name;
            }

            $this->onHookShortToOwner(Model::HOOK_BEFORE_INSERT, \Closure::fromCallable([$this, 'beforeInsert']), [], -5);
            $this->onHookShortToOwner(Model::HOOK_BEFORE_UPDATE, \Closure::fromCallable([$this, 'beforeUpdate']));
            $this->onHookShortToOwner(Model::HOOK_AFTER_DELETE, \Closure::fromCallable([$this, 'doDelete']));
            $this->onHookShortToOwner(Model::HOOK_AFTER_LOAD, \Closure::fromCallable([$this, 'afterLoad']));
        }
    }

    /**
     * Returns DSQL query.
     */
    public function dsql(): Query
    {
        $dsql = $this->getOwner()->persistence->initQuery($this->getOwner());

        return $dsql->reset('table')->table($this->foreign_table, $this->foreign_alias);
    }

    /**
     * Before query is executed, this method will be called.
     */
    public function initSelectQuery(Query $query): void
    {
        // if ON is set, we don't have to worry about anything
        if ($this->on) {
            $query->join(
                $this->foreign_table,
                $this->on instanceof \Atk4\Data\Persistence\Sql\Expression ? $this->on : $this->getOwner()->expr($this->on),
                $this->kind,
                $this->foreign_alias
            );

            return;
        }

        $query->join(
            $this->foreign_table,
            $this->getOwner()->expr('{{}}.{} = {}', [
                ($this->foreign_alias ?: $this->foreign_table),
                $this->foreign_field,
                $this->getOwner()->getField($this->master_field),
            ]),
            $this->kind,
            $this->foreign_alias
        );

        /*
        if ($this->reverse) {
            $query->field([$this->short_name => ($this->join ?:
                (
                    ($model->table_alias ?: $model->table)
                    .'.'.$this->master_field)
            )]);
        } else {
            $query->field([$this->short_name => $this->foreign_alias.'.'.$this->foreign_field]);
        }
         */
    }

    /**
     * Called from afterLoad hook.
     */
    public function afterLoad(): void
    {
        $model = $this->getOwner();

        // we need to collect ID
        if (isset($model->getDataRef()[$this->short_name])) {
            $this->id = $model->getDataRef()[$this->short_name];
            unset($model->getDataRef()[$this->short_name]);
        }
    }

    /**
     * Called from beforeInsert hook.
     */
    public function beforeInsert(array &$data): void
    {
        if ($this->weak) {
            return;
        }

        $model = $this->getOwner();

        // The value for the master_field is set, so we are going to use existing record anyway
        if ($model->hasField($this->master_field) && $model->get($this->master_field)) {
            return;
        }

        $query = $this->dsql();
        $query->mode('insert');
        $query->set($model->persistence->typecastSaveRow($model, $this->save_buffer));
        $this->save_buffer = [];
        $query->set($this->foreign_field, null);
        $query->insert();
        $this->id = $model->persistence->lastInsertId($model);

        if ($this->hasJoin()) {
            $this->getJoin()->set($this->master_field, $this->id);
        } else {
            $data[$this->master_field] = $this->id;
        }
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

        $model = $this->getOwner();

        $query = $this->dsql();
        $query->set($model->persistence->typecastSaveRow($model, $this->save_buffer));
        $this->save_buffer = [];
        $query->set($this->foreign_field, $this->hasJoin() ? $this->getJoin()->id : $id);
        $query->insert();
        $this->id = $model->persistence->lastInsertId($model);
    }

    /**
     * Called from beforeUpdate hook.
     */
    public function beforeUpdate(array &$data): void
    {
        if ($this->weak) {
            return;
        }

        if (!$this->save_buffer) {
            return;
        }

        $model = $this->getOwner();
        $query = $this->dsql();
        $query->set($model->persistence->typecastSaveRow($model, $this->save_buffer));
        $this->save_buffer = [];

        $id = $this->reverse ? $model->getId() : $model->get($this->master_field);

        $query->where($this->foreign_field, $id)->update();
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

        $model = $this->getOwner();
        $query = $this->dsql();
        $id = $this->reverse ? $model->getId() : $model->get($this->master_field);

        $query->where($this->foreign_field, $id)->delete();
    }
}
