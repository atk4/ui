<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

class ModelColor extends \atk4\data\Model
{
    public function init(): void
    {
        parent::init();

        $this->addField('name', [
            'type' => 'string',
            'ui' => [
                'table' => [
                    'Tooltip',
                    [
                        'tooltip_field' => 'note',
                        'icon' => 'info circle blue',
                    ],
                ],
            ],
        ]);

        $this->addField('value_not_always_present', [
            'type' => 'string',
            'ui' => [
                'table' => [
                    'NoValue',
                    [
                        'no_value' => ' no value ',
                    ],
                ],
            ],
        ]);

        $this->addField('key_value', [
            'type' => 'string',
            'values' => [
                1 => '1st val',
                '2nd val',
                '3rd val',
                '4th val',
            ],
            'ui' => [
                'table' => [
                    'KeyValue',
                ],
            ],
        ]);

        $this->addField('key_value_string', [
            'type' => 'string',
            'values' => [
                'one' => '1st val',
                'two' => '2nd val',
                'three' => '3rd val',
                'four' => '4th val',
            ],
            'ui' => [
                'table' => [
                    'KeyValue',
                ],
            ],
        ]);

        $this->addField('interests', [
            'type' => 'string',
            'ui' => [
                'table' => [
                    'Labels',
                ],
            ],
        ]);

        $this->addField('rating', [
            'type' => 'float',
            'ui' => [
                'table' => [
                    'ColorRating',
                    [
                        'min' => 1,
                        'max' => 3,
                        'steps' => 3,
                        'colors' => [
                            '#FF0000',
                            '#FFFF00',
                            '#00FF00',
                        ],
                    ],
                ],
            ],
        ]);

        $this->addField('note', ['system' => true]);
    }
}

$key_value_string = [
    1 => 'one',
    'two',
    'three',
    'four',
];

\atk4\ui\Header::addTo($app, ['Table column', 'subHeader' => 'Table column decorator can be set from your model.']);

$m = new ModelColor(new \atk4\data\Persistence\Static_([]));
foreach (range(1, 10) as $id) {
    $key_value = random_int(1, 4);

    $m->insert([
        'id' => $id,
        'name' => 'name ' . $id,
        'key_value' => $key_value,
        'key_value_string' => $key_value_string[$key_value],
        'value_not_always_present' => random_int(0, 100) > 50 ? 'have value' : '',
        'interests' => '1st label, 2nd label',
        'rating' => random_int(100, 300) / 100,
        'note' => 'lorem ipsum lorem dorem lorem',
    ]);
}

$table = \atk4\ui\Table::addTo($app);
$table->setModel($m);
