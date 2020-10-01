<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;
use atk4\ui\JsToast;

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
$control = $form->addControl('tree', [Form\Control\TreeItemSelector::class, 'treeItems' => $items, 'caption' => 'Multiple selection:'], ['type' => 'array', 'serialize' => 'json']);
$control->set($app->encodeJson([201, 301, 503]));

//$control->onItem(function($value) use ($app) {
//    return new \atk4\ui\JsToast($app->encodeJson($value));
//});

$control = $form->addControl('tree1', [Form\Control\TreeItemSelector::class, 'treeItems' => $items, 'allowMultiple' => false, 'caption' => 'Single selection:']);
$control->set(502);

//$control->onItem(function($tree) {
//    return new JsToast('Received 1');
//});

$form->onSubmit(function (Form $form) use ($app) {
    $response = [
        'multiple' => $form->model->get('tree'),
        'single' => $form->model->get('tree1'),
    ];

    $view = new \atk4\ui\Message('Items: ');
    $view->invokeInit();
    $view->text->addParagraph($app->encodeJson($response));

    return $view;
});
