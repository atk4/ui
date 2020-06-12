<?php

require_once __DIR__ . '/../atk-init.php';

$rules = [
    [
        'type' => 'text',
        'id' => 'vegetable',
        'label' => 'Vegetable'
    ],
    [
        'type' => 'radio',
        'id' => 'fruit',
        'label' => 'Fruit',
        'choices' => [
            ['label' => "Apple", 'value' => 'apple'],
            ['label' => 'Banana', 'value' => 'banana']
        ]
    ]
];

$f = \atk4\ui\Form::addTo($app);
$qb = $f->addField('qb', [\atk4\ui\FormField\ScopeBuilder::class]);
$qb->rules = $rules;