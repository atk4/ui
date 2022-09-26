<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\JsToast;
use Atk4\Ui\Message;

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

Header::addTo($app, ['Tree item selector']);

$form = Form::addTo($app);
$control = $form->addControl('tree', [Form\Control\TreeItemSelector::class, 'treeItems' => $items, 'caption' => 'Multiple selection:'], ['type' => 'json']);
$control->set([201, 301, 503]);

// $control->onItem(function (array $value) use ($app) {
//    return new JsToast($app->encodeJson($value));
// });

$control = $form->addControl('tree1', [Form\Control\TreeItemSelector::class, 'treeItems' => $items, 'allowMultiple' => false, 'caption' => 'Single selection:']);
$control->set(502);

// $control->onItem(function (int $value) {
//    return new JsToast('Received ' . $value);
// });

$form->onSubmit(function (Form $form) use ($app) {
    $response = [
        'multiple' => $form->model->get('tree'),
        'single' => $form->model->get('tree1'),
    ];

    $view = new Message('Items: ');
    $view->invokeInit();
    $view->text->addParagraph($app->encodeJson($response));

    return $view;
});
