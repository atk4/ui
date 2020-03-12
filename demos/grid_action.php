<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$country = new Country($db);

$g = $app->add('Grid');

$edit_executor = new atk4\ui\ActionExecutor\Form([
    'hasHeader' => false,
    'jsSuccess' => [
        new atk4\ui\jsExpression('$(".atk-dialog-content").parent().modal("hide")'),
        new atk4\ui\jsToast('Action Complete with success!'),
        $g->container->jsReload([$g->getName() . '_sort' => $g->getSortBy()]),
    ],
]);

$edit_action = $country->addAction('edit', [
    'callback' => [$country, 'save'],
    //'fields' => ['name', 'iso'],
    'ui' => ['Grid' => ['Executor' => $edit_executor, 'Button' => ['icon' => 'edit']]],
]);

$del_executor = new atk4\ui\ActionExecutor\Preview(
    [
    'previewType' => 'text',
    'hasHeader'   => false,
    'jsSuccess'   => function ($ex, $model) use ($g) {
        return [
            new atk4\ui\jsExpression('$(".atk-dialog-content").parent().modal("hide")'),
            new atk4\ui\jsToast('Record deleted with success!'),
            $g->table->jsRemoveRow($model->get('id')),
        ];
    },
]
);

$del_action = $country->addAction('delete', [
    'callback' => function ($m) {
        //$m->delete();
    },
    'preview' => function ($m) {
        return 'Will delete record: ' . $m->getTitle();
    },
    'ui' => ['Grid' => ['Executor' => $del_executor, 'Button' => ['icon' => 'delete']]],
]);

$g->setModel($country);
$g->ipp = 10;

$g->addUserAction($edit_action);
$g->addUserAction($del_action);

//$g->addHook('onUserAction', function($g, $page, $executor) {
//    $executor->form = $page->add('Form');
//    $executor->form->addField('test');
//});
