<?php

declare(strict_types=1);

namespace Atk4\Ui\View;

use Atk4\Data\Model;
use Atk4\Ui\Exception;

trait EntityTrait
{
    public ?Model $entity = null;

    /**
     * Associate this view with an entity. Do not place any logic in this method, instead take it to renderView().
     *
     * Do not try to create your own "Model" implementation, instead you must be looking for
     * your own "Persistence" implementation.
     *
     * @phpstan-assert !null $this->entity
     */
    public function setEntity(Model $entity): void
    {
        $entity->assertIsEntity();

        if ($this->entity !== null) {
            if ($this->entity === $entity) {
                return;
            }

            throw new Exception('Different entity is already set');
        }

        $this->entity = $entity;
    }
}
