<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\JsToast;

/** @var \Atk4\Ui\App $app */
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

\Atk4\Ui\Header::addTo($app, ['Tree item selector']);

$form = Form::addTo($app);
$control = $form->addControl('tree', [Form\Control\TreeItemSelector::class, 'treeItems' => $items, 'caption' => 'Multiple selection:'], ['type' => 'array', 'serialize' => 'json']);
$control->set($app->encodeJson([201, 301, 503]));

//$control->onItem(function($value) use ($app) {
//    return new \Atk4\Ui\JsToast($app->encodeJson($value));
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

    $view = new \Atk4\Ui\Message('Items: ');
    $view->invokeInit();
    $view->text->addParagraph($app->encodeJson($response));

    return $view;
});
