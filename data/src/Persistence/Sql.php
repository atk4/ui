<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence;

use Atk4\Data\Exception;
use Atk4\Data\Field;
use Atk4\Data\FieldSqlExpression;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Data\Persistence\Sql\Connection;
use Atk4\Data\Persistence\Sql\Exception as DsqlException;
use Atk4\Data\Persistence\Sql\Expression;
use Atk4\Data\Persistence\Sql\Query;
use Doctrine\DBAL\Platforms;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\SQLServer2012Platform;

class Sql extends Persistence
{
    /** @const string */
    public const HOOK_INIT_SELECT_QUERY = self::class . '@initSelectQuery';
    /** @const string */
    public const HOOK_BEFORE_INSERT_QUERY = self::class . '@beforeInsertQuery';
    /** @const string */
    public const HOOK_AFTER_INSERT_QUERY = self::class . '@afterInsertQuery';
    /** @const string */
    public const HOOK_BEFORE_UPDATE_QUERY = self::class . '@beforeUpdateQuery';
    /** @const string */
    public const HOOK_AFTER_UPDATE_QUERY = self::class . '@afterUpdateQuery';
    /** @const string */
    public const HOOK_BEFORE_DELETE_QUERY = self::class . '@beforeDeleteQuery';

    /**
     * Connection object.
     *
     * @var \Atk4\Data\Persistence\Sql\Connection
     */
    public $connection;

    /**
     * Default class when adding new field.
     *
     * @var array
     */
    public $_default_seed_addField = [\Atk4\Data\FieldSql::class];

    /**
     * Default class when adding hasOne field.
     *
     * @var array
     */
    public $_default_seed_hasOne = [\Atk4\Data\Reference\HasOneSql::class];

    /**
     * Default class when adding hasMany field.
     *
     * @var array
     */
    public $_default_seed_hasMany; // [\Atk4\Data\Reference\HasMany::class];

    /**
     * Default class when adding Expression field.
     *
     * @var array
     */
    public $_default_seed_addExpression = [FieldSqlExpression::class];

    /**
     * Default class when adding join.
     *
     * @var array
     */
    public $_default_seed_join = [Sql\Join::class];

    /**
     * Constructor.
     *
     * @param Connection|string $connection
     * @param string            $user
     * @param string            $password
     * @param array             $args
     */
    public function __construct($connection, $user = null, $password = null, $args = [])
    {
        if ($connection instanceof \Atk4\Data\Persistence\Sql\Connection) {
            $this->connection = $connection;

            return;
        }

        if (is_object($connection)) {
            throw (new Exception('You can only use Persistance_SQL with Connection class from Atk4\Data\Persistence\Sql'))
                ->addMoreInfo('connection', $connection);
        }

        // attempt to connect.
        $this->connection = \Atk4\Data\Persistence\Sql\Connection::connect(
            $connection,
            $user,
            $password,
            $args
        );
    }

    /**
     * Disconnect from database explicitly.
     */
    public function disconnect(): void
    {
        parent::disconnect();

        $this->connection = null; // @phpstan-ignore-line
    }

    /**
     * Returns Query instance.
     */
    public function dsql(): Query
    {
        return $this->connection->dsql();
    }

    /**
     * Atomic executes operations within one begin/end transaction, so if
     * the code inside callback will fail, then all of the transaction
     * will be also rolled back.
     *
     * @return mixed
     */
    public function atomic(\Closure $fx)
    {
        return $this->connection->atomic($fx);
    }

    public function getDatabasePlatform(): Platforms\AbstractPlatform
    {
        return $this->connection->getDatabasePlatform();
    }

    /**
     * {@inheritdoc}
     */
    public function add(Model $model, array $defaults = []): Model
    {
        // Use our own classes for fields, references and expressions unless
        // $defaults specify them otherwise.
        $defaults = array_merge([
            '_default_seed_addField' => $this->_default_seed_addField,
            '_default_seed_hasOne' => $this->_default_seed_hasOne,
            '_default_seed_hasMany' => $this->_default_seed_hasMany,
            '_default_seed_addExpression' => $this->_default_seed_addExpression,
            '_default_seed_join' => $this->_default_seed_join,
        ], $defaults);

        $model = parent::add($model, $defaults);

        if (!isset($model->table) || (!is_string($model->table) && $model->table !== false)) {
            throw (new Exception('Property $table must be specified for a model'))
                ->addMoreInfo('model', $model);
        }

        // When we work without table, we can't have any IDs
        if ($model->table === false) {
            $model->removeField($model->id_field);
            $model->addExpression($model->id_field, '1');
            //} else {
            // SQL databases use ID of int by default
            //$m->getField($m->id_field)->type = 'integer';
        }

        // Sequence support
        if ($model->sequence && $model->hasField($model->id_field)) {
            $model->getField($model->id_field)->default = $this->dsql()->mode('seq_nextval')->sequence($model->sequence);
        }

        return $model;
    }

    /**
     * Initialize persistence.
     */
    protected function initPersistence(Model $model): void
    {
        $model->addMethod('expr', static function (Model $m, ...$args) {
            return $m->persistence->expr($m, ...$args);
        });
        $model->addMethod('dsql', static function (Model $m, ...$args) {
            return $m->persistence->dsql($m, ...$args); // @phpstan-ignore-line
        });
        $model->addMethod('exprNow', static function (Model $m, ...$args) {
            return $m->persistence->exprNow($m, ...$args);
        });
    }

    /**
     * Creates new Expression object from expression string.
     *
     * @param mixed $expr
     */
    public function expr(Model $model, $expr, array $args = []): Expression
    {
        if (!is_string($expr)) {
            return $this->connection->expr($expr, $args);
        }
        preg_replace_callback(
            '/\[[a-z0-9_]*\]|{[a-z0-9_]*}/i',
            function ($matches) use (&$args, $model) {
                $identifier = substr($matches[0], 1, -1);
                if ($identifier && !isset($args[$identifier])) {
                    $args[$identifier] = $model->getField($identifier);
                }

                return $matches[0];
            },
            $expr
        );

        return $this->connection->expr($expr, $args);
    }

    /**
     * Creates new Query object with current_timestamp(precision) expression.
     */
    public function exprNow(int $precision = null): Expression
    {
        return $this->connection->dsql()->exprNow($precision);
    }

    /**
     * Initializes base query for model $m.
     */
    public function initQuery(Model $model): Query
    {
        $query = $model->persistence_data['dsql'] = $this->dsql();

        if ($model->table) {
            $query->table($model->table, $model->table_alias ?? null);
        }

        // add With cursors
        $this->initWithCursors($model, $query);

        return $query;
    }

    /**
     * Initializes WITH cursors.
     */
    public function initWithCursors(Model $model, Query $query): void
    {
        if (!$with = $model->with) {
            return;
        }

        foreach ($with as $alias => ['model' => $withModel, 'mapping' => $withMapping, 'recursive' => $recursive]) {
            // prepare field names
            $fieldsFrom = $fieldsTo = [];
            foreach ($withMapping as $from => $to) {
                $fieldsFrom[] = is_int($from) ? $to : $from;
                $fieldsTo[] = $to;
            }

            // prepare sub-query
            if ($fieldsFrom) {
                $withModel->onlyFields($fieldsFrom);
            }
            // 2nd parameter here strictly define which fields should be selected
            // as result system fields will not be added if they are not requested
            $subQuery = $withModel->action('select', [$fieldsFrom]);

            // add With cursor
            $query->with($subQuery, $alias, $fieldsTo ?: null, $recursive);
        }
    }

    /**
     * Adds Field in Query.
     */
    public function initField(Query $query, Field $field): void
    {
        $query->field($field, $field->useAlias() ? $field->short_name : null);
    }

    /**
     * Adds model fields in Query.
     *
     * @param array|false|null $fields
     */
    public function initQueryFields(Model $model, Query $query, $fields = null): void
    {
        // do nothing on purpose
        if ($fields === false) {
            return;
        }

        // init fields
        if (is_array($fields)) {
            // Set of fields is strictly defined for purposes of export,
            // so we will ignore even system fields.
            foreach ($fields as $fieldName) {
                $this->initField($query, $model->getField($fieldName));
            }
        } elseif ($model->only_fields) {
            $addedFields = [];

            // Add requested fields first
            foreach ($model->only_fields as $fieldName) {
                $field = $model->getField($fieldName);
                if ($field->never_persist) {
                    continue;
                }
                $this->initField($query, $field);
                $addedFields[$fieldName] = true;
            }

            // now add system fields, if they were not added
            foreach ($model->getFields() as $fieldName => $field) {
                if ($field->never_persist) {
                    continue;
                }
                if ($field->system && !isset($addedFields[$fieldName])) {
                    $this->initField($query, $field);
                }
            }
        } else {
            foreach ($model->getFields() as $fieldName => $field) {
                if ($field->never_persist) {
                    continue;
                }
                $this->initField($query, $field);
            }
        }
    }

    /**
     * Will set limit defined inside $m onto query $q.
     */
    protected function setLimitOrder(Model $model, Query $query): void
    {
        // set limit
        if ($model->limit && ($model->limit[0] || $model->limit[1])) {
            if ($model->limit[0] === null) {
                $model->limit[0] = \PHP_INT_MAX;
            }
            $query->limit($model->limit[0], $model->limit[1]);
        }

        // set order
        if ($model->order) {
            foreach ($model->order as $order) {
                $isDesc = strtolower($order[1]) === 'desc';

                if ($order[0] instanceof Expression) {
                    $query->order($order[0], $isDesc);
                } elseif (is_string($order[0])) {
                    $query->order($model->getField($order[0]), $isDesc);
                } else {
                    throw (new Exception('Unsupported order parameter'))
                        ->addMoreInfo('model', $model)
                        ->addMoreInfo('field', $order[0]);
                }
            }
        }
    }

    /**
     * Will apply $model->scope() conditions onto $query.
     */
    public function initQueryConditions(Model $model, Query $query): void
    {
        $this->_initQueryConditions($query, $model->getModel(true)->scope());

        // add entity ID to scope to allow easy traversal
        if ($model->isEntity() && $model->id_field && $model->getId() !== null) {
            $query->group($model->getField($model->id_field));
            if ($this->getDatabasePlatform() instanceof SQLServer2012Platform
                || $this->getDatabasePlatform() instanceof OraclePlatform) {
                foreach ($query->args['field'] as $alias => $field) {
                    $query->group(is_int($alias) ? $field : $alias);
                }
            }
            $query->having($model->getField($model->id_field), $model->getId());
        }
    }

    private function _initQueryConditions(Query $query, Model\Scope\AbstractScope $condition = null): void
    {
        if (!$condition->isEmpty()) {
            // peel off the single nested scopes to convert (((field = value))) to field = value
            $condition = $condition->simplify();

            // simple condition
            if ($condition instanceof Model\Scope\Condition) {
                $query->where(...$condition->toQueryArguments());
            }

            // nested conditions
            if ($condition instanceof Model\Scope) {
                $expression = $condition->isOr() ? $query->orExpr() : $query->andExpr();

                foreach ($condition->getNestedConditions() as $nestedCondition) {
                    $this->_initQueryConditions($expression, $nestedCondition);
                }

                $query->where($expression);
            }
        }
    }

    /**
     * Executing $model->action('update') will call this method.
     *
     * @return Query
     */
    public function action(Model $model, string $type, array $args = [])
    {
        $query = $this->initQuery($model);
        switch ($type) {
            case 'insert':
                return $query->mode('insert');
                // cannot apply conditions now

            case 'update':
                $query->mode('update');

                break;
            case 'delete':
                $query->mode('delete');
                $this->initQueryConditions($model, $query);
                $model->hook(self::HOOK_INIT_SELECT_QUERY, [$query, $type]);

                return $query;
            case 'select':
                $this->initQueryFields($model, $query, $args[0] ?? null);

                break;
            case 'count':
                $this->initQueryConditions($model, $query);
                $model->hook(self::HOOK_INIT_SELECT_QUERY, [$query, $type]);

                return $query->reset('field')->field('count(*)', $args['alias'] ?? null);
            case 'exists':
                $this->initQueryConditions($model, $query);
                $model->hook(self::HOOK_INIT_SELECT_QUERY, [$query, $type]);

                return $query->exists();
            case 'field':
                if (!isset($args[0])) {
                    throw (new Exception('This action requires one argument with field name'))
                        ->addMoreInfo('action', $type);
                }

                $field = is_string($args[0]) ? $model->getField($args[0]) : $args[0];
                $model->hook(self::HOOK_INIT_SELECT_QUERY, [$query, $type]);
                if (isset($args['alias'])) {
                    $query->reset('field')->field($field, $args['alias']);
                } elseif ($field instanceof FieldSqlExpression) {
                    $query->reset('field')->field($field, $field->short_name);
                } else {
                    $query->reset('field')->field($field);
                }
                $this->initQueryConditions($model, $query);
                $this->setLimitOrder($model, $query);

                if ($model->isEntity() && $model->loaded()) {
                    $query->where($model->getField($model->id_field), $model->getId());
                }

                return $query;
            case 'fx':
            case 'fx0':
                if (!isset($args[0], $args[1])) {
                    throw (new Exception('fx action needs 2 arguments, eg: ["sum", "amount"]'))
                        ->addMoreInfo('action', $type);
                }

                [$fx, $field] = $args;

                $field = is_string($field) ? $model->getField($field) : $field;

                $this->initQueryConditions($model, $query);
                $model->hook(self::HOOK_INIT_SELECT_QUERY, [$query, $type]);

                if ($type === 'fx') {
                    $expr = "{$fx}([])";
                } else {
                    $expr = "coalesce({$fx}([]), 0)";
                }

                if (isset($args['alias'])) {
                    $query->reset('field')->field($query->expr($expr, [$field]), $args['alias']);
                } elseif ($field instanceof FieldSqlExpression) {
                    $query->reset('field')->field($query->expr($expr, [$field]), $fx . '_' . $field->short_name);
                } else {
                    $query->reset('field')->field($query->expr($expr, [$field]));
                }

                return $query;
            default:
                throw (new Exception('Unsupported action mode'))
                    ->addMoreInfo('type', $type);
        }

        $this->initQueryConditions($model, $query);
        $this->setLimitOrder($model, $query);
        $model->hook(self::HOOK_INIT_SELECT_QUERY, [$query, $type]);

        return $query;
    }

    public function tryLoad(Model $model, $id): ?array
    {
        $noId = $id === self::ID_LOAD_ONE || $id === self::ID_LOAD_ANY;

        $query = $model->action('select');

        if (!$noId) {
            if (!$model->id_field) {
                throw (new Exception('Unable to load field by "id" when Model->id_field is not defined.'))
                    ->addMoreInfo('id', $id);
            }
            $query->where($model->getField($model->id_field), $id);
        }
        $query->limit($id === self::ID_LOAD_ANY ? 1 : 2);

        // execute action
        try {
            $rowsRaw = $query->getRows();
            if (count($rowsRaw) === 0) {
                return null;
            } elseif (count($rowsRaw) !== 1) {
                throw (new Exception('Ambiguous conditions, more than one record can be loaded.'))
                    ->addMoreInfo('model', $model)
                    ->addMoreInfo('id_field', $model->id_field)
                    ->addMoreInfo('id', $noId ? null : $id);
            }
            $data = $this->typecastLoadRow($model, $rowsRaw[0]);
        } catch (DsqlException $e) {
            throw (new Exception('Unable to load due to query error', 0, $e))
                ->addMoreInfo('query', $query->getDebugQuery())
                ->addMoreInfo('message', $e->getMessage())
                ->addMoreInfo('model', $model)
                ->addMoreInfo('scope', $model->scope()->toWords());
        }

        if ($model->id_field && !isset($data[$model->id_field])) {
            throw (new Exception('Model uses "id_field" but it was not available in the database'))
                ->addMoreInfo('model', $model)
                ->addMoreInfo('id_field', $model->id_field)
                ->addMoreInfo('id', $noId ? null : $id)
                ->addMoreInfo('data', $data);
        }

        return $data;
    }

    /**
     * Inserts record in database and returns new record ID.
     */
    public function insert(Model $model, array $data): string
    {
        $insert = $model->action('insert');

        if ($model->id_field && !isset($data[$model->id_field])) {
            unset($data[$model->id_field]);

            $this->syncIdSequence($model);
        }

        $insert->set($this->typecastSaveRow($model, $data));

        $st = null;
        try {
            $model->hook(self::HOOK_BEFORE_INSERT_QUERY, [$insert]);
            $st = $insert->execute();
        } catch (DsqlException $e) {
            throw (new Exception('Unable to execute insert query', 0, $e))
                ->addMoreInfo('query', $insert->getDebugQuery())
                ->addMoreInfo('message', $e->getMessage())
                ->addMoreInfo('model', $model)
                ->addMoreInfo('scope', $model->getModel(true)->scope()->toWords());
        }

        if ($model->id_field && isset($data[$model->id_field])) {
            $id = (string) $data[$model->id_field];

            $this->syncIdSequence($model);
        } else {
            $id = $this->lastInsertId($model);
        }

        $model->hook(self::HOOK_AFTER_INSERT_QUERY, [$insert, $st]);

        return $id;
    }

    /**
     * Export all DataSet.
     */
    public function export(Model $model, array $fields = null, bool $typecast = true): array
    {
        $data = $model->action('select', [$fields])->getRows();

        if ($typecast) {
            $data = array_map(function ($row) use ($model) {
                return $this->typecastLoadRow($model, $row);
            }, $data);
        }

        return $data;
    }

    public function prepareIterator(Model $model): \Traversable
    {
        $export = $model->action('select');

        try {
            return $export->getIterator();
        } catch (DsqlException $e) {
            throw (new Exception('Unable to execute iteration query', 0, $e))
                ->addMoreInfo('query', $export->getDebugQuery())
                ->addMoreInfo('message', $e->getMessage())
                ->addMoreInfo('model', $model)
                ->addMoreInfo('scope', $model->scope()->toWords());
        }
    }

    /**
     * Updates record in database.
     *
     * @param mixed $id
     */
    public function update(Model $model, $id, array $data): void
    {
        if (!$model->id_field) {
            throw new Exception('id_field of a model is not set. Unable to update record.');
        }

        $update = $this->initQuery($model);
        $update->mode('update');

        $data = $this->typecastSaveRow($model, $data);

        // only apply fields that has been modified
        $update->set($data);
        $update->where($model->getField($model->id_field), $id);

        $st = null;
        try {
            $model->hook(self::HOOK_BEFORE_UPDATE_QUERY, [$update]);
            if ($data) {
                $st = $update->execute();
            }
        } catch (DsqlException $e) {
            throw (new Exception('Unable to update due to query error', 0, $e))
                ->addMoreInfo('query', $update->getDebugQuery())
                ->addMoreInfo('message', $e->getMessage())
                ->addMoreInfo('model', $model)
                ->addMoreInfo('scope', $model->getModel(true)->scope()->toWords());
        }

        if (isset($data[$model->id_field]) && $model->getDirtyRef()[$model->id_field]) {
            // ID was changed
            $model->setId($data[$model->id_field]);
        }

        $model->hook(self::HOOK_AFTER_UPDATE_QUERY, [$update, $st]);

        // if any rows were updated in database, and we had expressions, reload
        if ($model->reload_after_save === true && (!$st || $st->rowCount())) {
            $d = $model->getDirtyRef();
            $model->reload();
            $model->_dirty_after_reload = $model->getDirtyRef();
            $dirtyRef = &$model->getDirtyRef();
            $dirtyRef = $d;
        }
    }

    /**
     * Deletes record from database.
     *
     * @param mixed $id
     */
    public function delete(Model $model, $id): void
    {
        if (!$model->id_field) {
            throw new Exception('id_field of a model is not set. Unable to delete record.');
        }

        $delete = $this->initQuery($model);
        $delete->mode('delete');
        $delete->where($model->getField($model->id_field), $id);
        $model->hook(self::HOOK_BEFORE_DELETE_QUERY, [$delete]);

        try {
            $delete->execute();
        } catch (DsqlException $e) {
            throw (new Exception('Unable to delete due to query error', 0, $e))
                ->addMoreInfo('query', $delete->getDebugQuery())
                ->addMoreInfo('message', $e->getMessage())
                ->addMoreInfo('model', $model)
                ->addMoreInfo('scope', $model->getModel(true)->scope()->toWords());
        }
    }

    public function getFieldSqlExpression(Field $field, Expression $expression): Expression
    {
        if (isset($field->getOwner()->persistence_data['use_table_prefixes'])) {
            $mask = '{{}}.{}';
            $prop = [
                $field->hasJoin()
                    ? ($field->getJoin()->foreign_alias ?: $field->getJoin()->short_name)
                    : ($field->getOwner()->table_alias ?: $field->getOwner()->table),
                $field->getPersistenceName(),
            ];
        } else {
            // references set flag use_table_prefixes, so no need to check them here
            $mask = '{}';
            $prop = [
                $field->getPersistenceName(),
            ];
        }

        // If our Model has expr() method (inherited from Persistence\Sql) then use it
        if ($field->getOwner()->hasMethod('expr')) {
            return $field->getOwner()->expr($mask, $prop);
        }

        // Otherwise call method from expression
        return $expression->expr($mask, $prop);
    }

    private function getIdSequenceName(Model $model): ?string
    {
        $sequenceName = $model->sequence ?: null;

        if ($sequenceName === null) {
            // PostgreSQL uses sequence internally for PK autoincrement,
            // use default name if not set explicitly
            if ($this->connection instanceof \Atk4\Data\Persistence\Sql\Postgresql\Connection) {
                $sequenceName = $model->table . '_' . $model->id_field . '_seq';
            }
        }

        return $sequenceName;
    }

    public function lastInsertId(Model $model): string
    {
        // TODO: Oracle does not support lastInsertId(), only for testing
        // as this does not support concurrent inserts
        if ($this->connection instanceof \Atk4\Data\Persistence\Sql\Oracle\Connection) {
            if (!$model->id_field) {
                return ''; // TODO code should never call lastInsertId() if id field is not defined
            }

            $query = $this->connection->dsql()->table($model->table);
            $query->field($query->expr('max({id_col})', ['id_col' => $model->id_field]), 'max_id');

            return $query->getOne();
        }

        return $this->connection->lastInsertId($this->getIdSequenceName($model));
    }

    protected function syncIdSequence(Model $model): void
    {
        // PostgreSQL sequence must be manually synchronized if a row with explicit ID was inserted
        if ($this->connection instanceof \Atk4\Data\Persistence\Sql\Postgresql\Connection) {
            $this->connection->expr(
                'select setval([], coalesce(max({}), 0) + 1, false) from {}',
                [$this->getIdSequenceName($model), $model->id_field, $model->table]
            )->execute();
        }
    }
}
