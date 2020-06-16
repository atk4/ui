<?php

require_once __DIR__ . '/../init-app.php';

$query = [
    'logicalOperator' => 'all',
    'children' => [
        [
            'type' => 'query-builder-rule',
            'query' => [
                'rule' => 'date',
                'operator' => '=',
                'operand' => 'Date',
                'value' => '2020-06-18'
            ],
        ],
        [
            'type' => 'query-builder-rule',
            'query' => [
                'rule' => 'vegetable',
                'operator' => 'contains',
                'operand' => 'Vegetable',
                'value' => null
            ],
        ]
    ]
];

$rules = [
    [
        'type' => 'text',
        'id' => 'vegetable',
        'label' => 'Vegetable',
    ],
    [
        'type' => 'custom-component',
        'component' => 'DatePicker',
        'inputType' => 'date',
        'id' => 'date',
        'label' => 'Date',
        'operators' => ['=', '<', '>'],
        'default' => null,
//        'format' => "YYYY-MMM-DD", // extra component option
//        'locale' => 'fr-FR'        // extra component option
    ],
    [
        'type' => 'numeric',
        'inputType' => 'number',
        'id' => 'count',
        'label' => 'Count',
    ],
    [
        'type' => 'radio',
        'id' => 'fruit',
        'label' => 'Fruit',
        'choices' => [
            ['label' => 'Apple', 'value' => 'apple'],
            ['label' => 'Banana', 'value' => 'banana'],
        ],
    ],
];

$f = \atk4\ui\Form::addTo($app);
$qb = $f->addField('qb', [\atk4\ui\FormField\ScopeBuilder::class]);
$qb->rules = $rules;
$qb->query = $query;

$f->onSubmit(function($f) {
   echo  '<pre>' . json_encode($f->model->get('qb'), JSON_PRETTY_PRINT) . '</pre>';
});
