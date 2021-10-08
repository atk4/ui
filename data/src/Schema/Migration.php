<?php

declare(strict_types=1);

namespace Atk4\Data\Schema;

use Atk4\Core\Exception;
use Atk4\Data\Field;
use Atk4\Data\FieldSqlExpression;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Data\Persistence\Sql\Connection;
use Atk4\Data\Reference\HasOne;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;

class Migration
{
    public const REF_TYPE_NONE = 0;
    public const REF_TYPE_LINK = 1;
    public const REF_TYPE_PRIMARY = 2;

    /** @var Connection */
    public $connection;

    /** @var Table */
    public $table;

    /**
     * Create new migration.
     *
     * @param Connection|Persistence|Model $source
     */
    public function __construct($source)
    {
        if (func_num_args() > 1) {
            throw new \Error();
        }

        if ($source instanceof Connection) {
            $this->connection = $source;
        } elseif ($source instanceof Persistence\Sql) {
            $this->connection = $source->connection;
        } elseif ($source instanceof Model && $source->persistence instanceof Persistence\Sql) {
            $this->connection = $source->persistence->connection;
        } else {
            throw (new Exception('Source is specified incorrectly. Must be Connection, Persistence or initialized Model'))
                ->addMoreInfo('source', $source);
        }

        if ($source instanceof Model && $source->persistence instanceof Persistence\Sql) {
            $this->setModel($source);
        }
    }

    protected function getDatabasePlatform(): AbstractPlatform
    {
        return $this->connection->getDatabasePlatform();
    }

    protected function getSchemaManager(): AbstractSchemaManager
    {
        return $this->connection->connection()->getSchemaManager();
    }

    public function table(string $tableName): self
    {
        $this->table = new Table($this->getDatabasePlatform()->quoteSingleIdentifier($tableName));

        return $this;
    }

    private function getPrimaryKeyColumn(): ?Column
    {
        if ($this->table->getPrimaryKey() === null) {
            return null;
        }

        return $this->table->getColumn($this->table->getPrimaryKey()->getColumns()[0]);
    }

    public function create(): self
    {
        $this->getSchemaManager()->createTable($this->table);

        $pkColumn = $this->getPrimaryKeyColumn();
        if ($this->getDatabasePlatform() instanceof OraclePlatform && $pkColumn !== null) {
            $this->connection->expr(
                <<<'EOT'
                    begin
                        execute immediate [];
                    end;
                    EOT,
                [
                    $this->connection->expr(
                        <<<'EOT'
                            create or replace trigger {table_ai_trigger_before}
                                before insert on {table}
                                for each row
                                when (new.{id_column} is null)
                            declare
                                last_id {table}.{id_column}%type;
                            begin
                                select nvl(max({id_column}), 0) into last_id from {table};
                                :new.{id_column} := last_id + 1;
                            end;
                            EOT,
                        [
                            'table' => $this->table->getName(),
                            'table_ai_trigger_before' => $this->table->getName() . '__aitb',
                            'id_column' => $pkColumn->getName(),
                        ]
                    )->render(),
                ]
            )->execute();
        }

        return $this;
    }

    public function drop(): self
    {
        if ($this->getDatabasePlatform() instanceof OraclePlatform) {
            // drop trigger if exists
            // see https://stackoverflow.com/questions/1799128/oracle-if-table-exists
            $this->connection->expr(
                <<<'EOT'
                    begin
                        execute immediate [];
                    exception
                        when others then
                            if sqlcode != -4080 then
                                raise;
                            end if;
                    end;
                    EOT,
                [
                    $this->connection->expr(
                        'drop trigger {table_ai_trigger_before}',
                        [
                            'table_ai_trigger_before' => $this->table->getName() . '__aitb',
                        ]
                    )->render(),
                ]
            )->execute();
        }

        $this->getSchemaManager()->dropTable($this->getDatabasePlatform()->quoteSingleIdentifier($this->table->getName()));

        return $this;
    }

    public function dropIfExists(): self
    {
        try {
            $this->drop();
        } catch (\Doctrine\DBAL\Exception|\Doctrine\DBAL\DBALException $e) {
        }

        return $this;
    }

    public function field(string $fieldName, array $options = []): self
    {
        if ($options['type'] === 'time' && $this->getDatabasePlatform() instanceof OraclePlatform) {
            $options['type'] = 'string';
        }

        $refType = $options['ref_type'] ?? self::REF_TYPE_NONE;
        unset($options['ref_type']);

        $column = $this->table->addColumn($this->getDatabasePlatform()->quoteSingleIdentifier($fieldName), $options['type'] ?? 'string');

        if (!($options['mandatory'] ?? false) && $refType !== self::REF_TYPE_PRIMARY) {
            $column->setNotnull(false);
        }

        if ($column->getType()->getName() === 'integer' && $refType !== self::REF_TYPE_NONE) {
            $column->setUnsigned(true);
        }

        if (in_array($column->getType()->getName(), ['string', 'text'], true)) {
            if ($this->getDatabasePlatform() instanceof SqlitePlatform) {
                $column->setPlatformOption('collation', 'NOCASE');
            }
        }

        if ($refType === self::REF_TYPE_PRIMARY) {
            $this->table->setPrimaryKey([$this->getDatabasePlatform()->quoteSingleIdentifier($fieldName)]);
            if (!$this->getDatabasePlatform() instanceof OraclePlatform) {
                $column->setAutoincrement(true);
            }
        }

        return $this;
    }

    public function id(string $name = 'id'): self
    {
        $options = [
            'type' => 'integer',
            'ref_type' => self::REF_TYPE_PRIMARY,
        ];

        $this->field($name, $options);

        return $this;
    }

    public function setModel(Model $model): Model
    {
        $this->table($model->table);

        foreach ($model->getFields() as $field) {
            if ($field->never_persist || $field instanceof FieldSqlExpression) {
                continue;
            }

            if ($field->short_name === $model->id_field) {
                $refype = self::REF_TYPE_PRIMARY;
                $persistField = $field;
            } else {
                $refField = $this->getReferenceField($field);
                $refype = $refField !== null ? self::REF_TYPE_LINK : $refype = self::REF_TYPE_NONE;
                $persistField = $refField ?? $field;
            }

            $options = [
                'type' => $refype !== self::REF_TYPE_NONE && empty($persistField->type) ? 'integer' : $persistField->type,
                'ref_type' => $refype,
                'mandatory' => ($field->mandatory || $field->required) && ($persistField->mandatory || $persistField->required),
            ];

            $this->field($field->getPersistenceName(), $options);
        }

        return $model;
    }

    protected function getReferenceField(Field $field): ?Field
    {
        $reference = $field->getReference();
        if ($reference instanceof HasOne) {
            $referenceTheirField = \Closure::bind(function () use ($reference) {
                return $reference->their_field;
            }, null, \Atk4\Data\Reference::class)();

            $referenceField = $referenceTheirField ?? $reference->getOwner()->id_field;

            $modelSeed = is_array($reference->model)
                ? $reference->model
                : [get_class($reference->model)];
            $referenceModel = Model::fromSeed($modelSeed, [new Persistence\Sql($this->connection)]);

            return $referenceModel->getField($referenceField);
        }

        return null;
    }
}
