<?php

declare(strict_types=1);

namespace Atk4\Data\Action;

/**
 * @internal
 *
 * @phpstan-extends \IteratorIterator<int, array, \Traversable<array>>
 */
final class RenameColumnIterator extends \IteratorIterator
{
    /** @var string */
    protected $origName;
    /** @var string */
    protected $newName;

    /**
     * @param \Traversable<array> $iterator
     */
    public function __construct(\Traversable $iterator, string $origName, string $newName)
    {
        parent::__construct($iterator);

        $this->origName = $origName;
        $this->newName = $newName;
    }

    public function current(): array
    {
        $row = parent::current();

        $keys = array_keys($row);
        $keys[array_search($this->origName, $keys, true)] = $this->newName;

        return array_combine($keys, $row);
    }
}
