<?php

require 'init.php';

$items = [
    [
        'name'  => 'Electronics',
        'nodes' => [
            [
                'name'  => 'Phone',
                'nodes' => [
                    [
                        'name' => 'iPhone',
                        'id'   => 502,
                    ],
                    [
                        'name' => 'Google Pixels',
                        'id'   => 503,
                    ],
                ],
            ],
            ['name' => 'Tv', 'id' => 501, 'nodes' => []],
            ['name' => 'Radio', 'id' => 601, 'nodes' => []],
        ],
    ],
    ['name' => 'Cleaner', 'id' => 201, 'nodes' => []],
    ['name' => 'Appliances', 'id' => 301, 'nodes' => []],
];

$empty = [];

$app->add(['Header', 'Item selector']);

$f = $app->add('Form');
$field = $f->addField('tree', [new \atk4\ui\FormField\TreeItemSelector(['treeItems' => $items]), 'caption' => 'Select items:'], ['type' => 'array', 'serialize' => 'json']);
$field->set([201, 301, 503]);

//$field->onItem(function($value) {
//    return new \atk4\ui\jsToast(json_encode($value));
//});

$field1 = $f->addField('tree1', [new \atk4\ui\FormField\TreeItemSelector(['treeItems' => $items, 'allowMultiple' => false]), 'caption' => 'Select One item:']);
$field1->set(502);

//$field1->onItem(function($tree) {
//    return new jsToast('Received 1');
//});

$f->onSubmit(function ($f) {
    $resp = [
        'multiple' => $f->model->get('tree'),
        'single'   => $f->model->get('tree1'),
    ];

    return new \atk4\ui\jsToast(json_encode($resp));
});
