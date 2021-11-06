<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Data\Field;
use Atk4\Data\Model;

class EntityFieldProxy
{
    /** @var Model */
    private $entity;
    /** @var string */
    private $fieldName;

    public function __construct(Model $entity, string $fieldName)
    {
        $entity->assertIsEntity();

        $this->entity = $entity;
        $this->fieldName = $fieldName;
    }

    public function getEntity(): Model
    {
        return $this->entity;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getField(): Field
    {
        return $this->getEntity()->getModel()->getField($this->getFieldName());
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->getEntity()->get($this->getFieldName());
    }

    /**
     * @param mixed $value
     */
    public function set($value): void
    {
        $this->getEntity()->set($this->getFieldName(), $value);
    }

    public function setNull(): void
    {
        $this->getEntity()->setNull($this->getFieldName());
    }
}
