<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Header;
use Atk4\Ui\Table;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$modelColorClass = AnonymousClassNameCache::get_class(fn () => new class() extends Model {
    protected function init(): void
    {
        parent::init();

        $this->addField('name', [
            'type' => 'string',
            'ui' => [
                'table' => [
                    Table\Column\Tooltip::class,
                    [
                        'tooltipField' => 'note',
                        'icon' => 'info circle blue',
                    ],
                ],
            ],
        ]);

        $this->addField('value_not_always_present', [
            'type' => 'string',
            'ui' => [
                'table' => [
                    Table\Column\NoValue::class,
                    [
                        'noValue' => ' no value ',
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
                    Table\Column\KeyValue::class,
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
                    Table\Column\KeyValue::class,
                ],
            ],
        ]);

        $this->addField('interests', [
            'type' => 'string',
            'ui' => [
                'table' => [
                    Table\Column\Labels::class,
                ],
            ],
        ]);

        $this->addField('rating', [
            'type' => 'float',
            'ui' => [
                'table' => [
                    Table\Column\ColorRating::class,
                    [
                        'min' => 1,
                        'max' => 3,
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
});

$keyValueString = [
    1 => 'one',
    'two',
    'three',
    'four',
];

Header::addTo($app, ['Table column', 'subHeader' => 'Table column decorator can be set from your model.']);

$model = new $modelColorClass(new Persistence\Static_([]));

foreach (range(1, 10) as $id) {
    $keyValue = random_int(1, 4);

    $model->insert([
        'id' => $id,
        'name' => 'name ' . $id,
        'key_value' => $keyValue,
        'key_value_string' => $keyValueString[$keyValue],
        'value_not_always_present' => random_int(0, 100) > 50 ? 'have value' : '',
        'interests' => '1st label, 2nd label',
        'rating' => random_int(100, 300) / 100,
        'note' => $id !== 3 ? 'lorem ipsum lorem dorem lorem' : null,
    ]);
}

$table = Table::addTo($app);
$table->setModel($model);
