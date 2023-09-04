<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsToast;
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
                        'name' => 'Google Pixel',
                        'id' => 503,
                    ],
                ],
            ],
            ['name' => 'Tv', 'id' => 501, 'nodes' => []],
            ['name' => 'Radio', 'id' => 601],
        ],
    ],
    ['name' => 'Cleaner', 'id' => 201],
    ['name' => 'Appliances', 'id' => 301],
];

$pathFromIdFx = static function (array $items, int $id) use (&$pathFromIdFx): ?string {
    foreach ($items as $item) {
        if (($item['id'] ?? false) === $id) {
            return $item['name'];
        }

        $itemRes = $pathFromIdFx($item['nodes'] ?? [], $id);
        if ($itemRes !== null) {
            return $item['name'] . ' > ' . $itemRes;
        }
    }

    return null;
};

Header::addTo($app, ['Tree item selector']);

$form = Form::addTo($app);
$control = $form->addControl('tree', [Form\Control\TreeItemSelector::class, 'treeItems' => $items, 'caption' => 'Multiple selection:'], ['type' => 'json']);
$control->set([201, 301, 503]);

$control->onItem(static function (array $values) use ($pathFromIdFx, $items) {
    return new JsToast('Selected: ' . implode(',<br>', array_map(static fn ($v) => $pathFromIdFx($items, $v), $values)));
});

$control = $form->addControl('tree1', [Form\Control\TreeItemSelector::class, 'treeItems' => $items, 'allowMultiple' => false, 'caption' => 'Single selection:']);
$control->set(503);

$control->onItem(static function (int $value) use ($pathFromIdFx, $items) {
    return new JsToast('Selected: ' . $pathFromIdFx($items, $value));
});

$form->onSubmit(static function (Form $form) use ($app) {
    $response = [
        'multiple' => $form->model->get('tree'),
        'single' => $form->model->get('tree1'),
    ];

    $view = new Message('Items:');
    $view->setApp($form->getApp());
    $view->invokeInit();
    $view->text->addParagraph($app->encodeJson($response));

    return $view;
});
