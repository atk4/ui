<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Array_\Db;

use Atk4\Data\Exception;

class Table
{
    /** @var string Immutable */
    private $tableName;
    /** @var array<string, string> */
    private $columnNames = [];
    /** @var array<int, Row> */
    private $rows = [];

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function __debugInfo(): array
    {
        return [
            'table_name' => $this->getTableName(),
            'column_names' => $this->getColumnNames(),
            'row_count' => count($this->rows),
        ];
    }

    /**
     * @param string $name
     */
    protected function assertValidIdentifier($name): void
    {
        if (!is_string($name) || !$name || is_numeric($name)) {
            throw (new Exception('Name must be a non-empty non-numeric string'))
                ->addMoreInfo('name', $name);
        }
    }

    /**
     * @param mixed $value
     */
    protected function assertValidValue($value): void
    {
        if ($value instanceof self || $value instanceof Row) {
            throw new Exception('Value cannot be an ' . get_class($value) . ' object');
        } elseif (!is_scalar($value) && $value !== null) {
            throw (new Exception('Value must be scalar'))
                ->addMoreInfo('value', $value);
        }
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function hasColumnName(string $columnName): bool
    {
        return isset($this->columnNames[$columnName]);
    }

    public function assertHasColumnName(string $columnName): void
    {
        if (!isset($this->columnNames[$columnName])) {
            throw (new Exception('Column name does not exist'))
                ->addMoreInfo('table_name', $this->getTableName())
                ->addMoreInfo('column_name', $columnName);
        }
    }

    /**
     * @return $this
     */
    public function addColumnName(string $columnName): self
    {
        $this->assertValidIdentifier($columnName);
        if (isset($this->columnNames[$columnName])) {
            throw (new Exception('Column name is already present'))
                ->addMoreInfo('table_name', $this->getTableName())
                ->addMoreInfo('column_name', $columnName);
        }

        $this->columnNames[$columnName] = $columnName;

        foreach ($this->getRows() as $row) {
            \Closure::bind(function () use ($row, $columnName) {
                $row->initValue($columnName);
            }, null, $row)();
        }

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getColumnNames(): array
    {
        return array_values($this->columnNames);
    }

    public function hasRow(int $rowIndex): bool
    {
        return isset($this->rows[$rowIndex]);
    }

    public function getRow(int $rowIndex): Row
    {
        if (!isset($this->rows[$rowIndex])) {
            throw (new Exception('Row with given index was not found'))
                ->addMoreInfo('table_name', $this->getTableName())
                ->addMoreInfo('row_index', $rowIndex);
        }

        return $this->rows[$rowIndex];
    }

    /**
     * @param class-string<Row>    $rowClass
     * @param array<string, mixed> $rowData
     */
    public function addRow(string $rowClass, array $rowData): Row
    {
        $that = $this;
        $columnNames = $this->getColumnNames();
        /** @var Row $row */
        $row = \Closure::bind(function () use ($that, $rowClass, $columnNames) {
            $row = new $rowClass($that);
            foreach ($columnNames as $columnName) {
                $row->initValue($columnName); // @phpstan-ignore-line
            }

            return $row;
        }, null, $rowClass)();
        $this->rows[$row->getRowIndex()] = $row;

        foreach ($rowData as $columnName => $value) {
            if (!$this->hasColumnName($columnName)) {
                $this->addColumnName($columnName);
            }
        }

        $row->updateValues($rowData);

        return $row;
    }

    public function deleteRow(Row $row): void
    {
        \Closure::bind(function () use ($row) {
            $row->beforeDelete();
        }, null, $row)();

        unset($this->rows[$row->getRowIndex()]);
    }

    /**
     * @return \Traversable<Row>
     */
    public function getRows(): \Traversable
    {
        return new \ArrayIterator($this->rows);
    }

    /**
     * @param array<string, mixed> $newRowData
     */
    protected function beforeValuesSet(Row $childRow, $newRowData): void
    {
        foreach ($newRowData as $columnName => $newValue) {
            $this->assertValidValue($newValue);

            // update index here
        }
    }

    /**
     * TODO rewrite with hash index support.
     *
     * @param mixed $id
     */
    public function getRowById(\Atk4\Data\Model $model, $id): ?Row
    {
        foreach ($this->getRows() as $row) {
            if ($row->getValue($model->id_field) === $id) {
                return $row;
            }
        }

        return null;
    }
}
