<?php

declare(strict_types=1);

namespace Atk4\Ui\Persistence;

use Atk4\Data\Model;
use Atk4\Data\Persistence;

class Post extends Persistence
{
    public function load(Model $model, $id): array
    {
        // carefully copy stuff from $_POST into the model
        $dataRaw = [$model->idField => $id];

        foreach ($model->getFields() as $field => $def) {
            if (isset($_POST[$field])) {
                $dataRaw[$field] = $_POST[$field];
            }
        }

        $data = $this->typecastLoadRow($model, $dataRaw);

        return $data;
    }
}
