<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$items = [
    [
        'name' => 'Electronics',
        'nodes' => [
            [
                'name' => 'Phone',
                'nodes' => [
                    [
                        'name' => 'iPhone',
                        'id' => 502,
                    ],
                    [
                        'name' => 'Google Pixels',
                        'id' => 503,
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

\atk4\ui\Header::addTo($app, ['Tree item selector']);

$form = Form::addTo($app);
$field = $form->addControl('tree', [new Form\Control\TreeItemSelector(['treeItems' => $items]), 'caption' => 'Multiple selection:'], ['type' => 'array', 'serialize' => 'json']);
$field->set(json_encode([201, 301, 503]));

//$field->onItem(function($value) {
//    return new \atk4\ui\jsToast(json_encode($value));
//});

$field1 = $form->addControl('tree1', [new Form\Control\TreeItemSelector(['treeItems' => $items, 'allowMultiple' => false, 'caption' => 'Single selection:']), ['type' => 'array']]);
$field1->set(502);

//$field1->onItem(function($tree) {
//    return new jsToast('Received 1');
//});

$form->onSubmit(function (Form $form) {
    $response = [
        'multiple' => $form->model->get('tree'),
        'single' => $form->model->get('tree1'),
    ];

    return print_r(json_encode($response, JSON_PRETTY_PRINT));
});
