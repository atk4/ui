<?php

namespace atk4\ui\Persistence;

use atk4\data\Model;

class POST extends \atk4\data\Persistence
{
    public function load(Model $m, $id = 0)
    {
        // carefully copy stuff from $_POST into the model
        $data = [];

        foreach ($m->getFields() as $field => $def) {
            if ($def->type === 'boolean') {
                $data[$field] = isset($_POST[$field]);
                continue;
            }

            if (isset($_POST[$field])) {
                $data[$field] = $_POST[$field];
            }
        }

        return array_merge($m->data, $data);
    }
}
