<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Array_\Db;

class Row
{
    /** @var int */
    private static $nextRowIndex = -1;

    /** @var Table Immutable */
    private $owner;
    /** @var int Immutable */
    private $rowIndex;
    /** @var array<string, mixed> */
    private $data = [];

    public function __construct(Table $owner)
    {
        $this->owner = $owner;
        $this->rowIndex = self::getNextRowIndex();
    }

    public static function getNextRowIndex(): int
    {
        return ++self::$nextRowIndex;
    }

    public function __debugInfo(): array
    {
        return [
            'row_index' => $this->getRowIndex(),
            'data' => $this->getData(),
        ];
    }

    public function getOwner(): Table
    {
        return $this->owner;
    }

    public function getRowIndex(): int
    {
        return $this->rowIndex;
    }

    /**
     * @return mixed
     */
    public function getValue(string $columnName)
    {
        return $this->data[$columnName];
    }

    public function getData(): array
    {
        return $this->data;
    }

    protected function initValue(string $columnName): void
    {
        $this->data[$columnName] = null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateValues(array $data): void
    {
        $owner = $this->getOwner();

        $newData = [];
        foreach ($data as $columnName => $newValue) {
            $owner->assertHasColumnName($columnName);
            if ($newValue !== $this->data[$columnName]) {
                $newData[$columnName] = $newValue;
            }
        }

        $that = $this;
        \Closure::bind(function () use ($owner, $that, $newData) {
            $owner->beforeValuesSet($that, $newData);
        }, null, $owner)();

        foreach ($newData as $columnName => $newValue) {
            $this->data[$columnName] = $newValue;
        }
    }

    protected function beforeDelete(): void
    {
        $this->updateValues(array_map(function () {
            return null;
        }, $this->data));

        $this->owner = null; // @phpstan-ignore-line
    }
}
