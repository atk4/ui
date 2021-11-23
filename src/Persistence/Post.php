<?php

declare(strict_types=1);

namespace Atk4\Ui\Persistence;

use Atk4\Data\Model;
use Atk4\Data\Persistence;

class Post extends Persistence
{
    public function load(Model $model, $id = 0): array
    {
        // carefully copy stuff from $_POST into the model
        $data = [];

        foreach ($model->getFields() as $field => $def) {
            if ($def->type === 'boolean') {
                $data[$field] = isset($_POST[$field]);

                continue;
            }

            if (isset($_POST[$field])) {
                $data[$field] = $_POST[$field];
            }
        }

        // TODO typecast!

//        return array_merge($model->get(), $data);
        return $data;
    }
}
