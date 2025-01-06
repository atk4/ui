<?php

declare(strict_types=1);

namespace Atk4\Ui\View;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Exception;

trait ModelTrait
{
    public ?Model $model = null;

    /**
     * Associate this view with a model. Do not place any logic in this method, instead take it to renderView().
     *
     * Do not try to create your own "Model" implementation, instead you must be looking for
     * your own "Persistence" implementation.
     *
     * @phpstan-assert !null $this->model
     */
    public function setModel(Model $model): void
    {
        $model->assertIsModel();

        if ($this->model !== null) {
            if ($this->model === $model) {
                return;
            }

            throw new Exception('Different model is already set');
        }

        $this->model = $model;
    }

    /**
     * Set source for this view.
     *
     * @param array<int|string, mixed> $data
     * @param list<string>             $fields Limit model to particular fields
     *
     * @phpstan-assert !null $this->model
     */
    public function setSource(array $data, $fields = null): void
    {
        $model = new Model();

        // ID with zero value is not supported (at least in MySQL replaces it with next AI value)
        if (isset($data[0])) {
            if (array_is_list($data)) {
                $oldData = $data;
                $data = [];
                foreach ($oldData as $k => $row) {
                    $data[$k + 1_000_000_000] = $row; // large offset to prevent accessing wrong data by old key
                }
            } else {
                throw new Exception('Source data contains unsupported zero key');
            }
        } else {
            $model->addField('id', ['type' => 'string']); // TODO probably unwanted
        }

        $model->setPersistence(new Persistence\Static_($data));

        $this->setModel($model, $fields); // @phpstan-ignore arguments.count
    }
}
