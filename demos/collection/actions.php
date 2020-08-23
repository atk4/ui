<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Button;
use atk4\ui\UserAction;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Button::addTo($app, ['js Event Executor', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['jsactions']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Extensions to ATK Data Actions', 'subHeader' => 'Demonstrate how to augment your models with actions and vizualize those inside UI']);

// Actions can be added easily to the model

$files = new FileLock($app->db);

// This action must appear on top of the Crud
$action = $files->addUserAction(
    'import_from_filesystem',
    [
        'callback' => 'importFromFilesystem',
        'preview' => function ($model, $path) {
            return 'Considering path: ' . $path;
        },
        'args' => [
            'path' => ['type' => 'string', 'required' => true],
        ],
        'appliesTo' => \atk4\data\Model\UserAction::APPLIES_TO_NO_RECORDS,
    ]
);

$files->addUserAction('download', function (\atk4\data\Model $model) {
    $len = strlen(file_get_contents($model->get('name')));

    return "{$len} bytes downloaded..";
});

//$files->getUserAction('download')->system = true;

$app->add($grid = new \atk4\ui\GridLayout(['columns' => 3]));

$executor = $grid->add(new UserAction\BasicExecutor(['executorButton' => [Button::class, 'Import', 'primary']]), 'r1c1');
$executor->setAction($action);
$executor->ui = 'segment';
$executor->description = 'Execute action using "BasicExecutor" and path="." argument';
$executor->setArguments(['path' => '.']);
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($x) {
    return new \atk4\ui\JsToast('Done!');
});

$executor = $grid->add(new UserAction\ArgumentFormExecutor(), 'r1c2');
$executor->setAction($action);
$executor->description = 'ArgumentFormExecutor will ask user about arguments';
$executor->ui = 'segment';
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($x, $ret) {
    return new \atk4\ui\JsToast('Imported!');
});

$executor = $grid->add(new \atk4\ui\UserAction\PreviewExecutor(), 'r1c3');
$executor->setAction($action);
$executor->ui = 'segment';
$executor->previewType = 'console';
$executor->description = 'Displays preview in console prior to executing';
$executor->setArguments(['path' => '.']);
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($x, $ret) {
    return new \atk4\ui\JsToast('Confirm!');
});

\atk4\ui\Crud::addTo($app, ['ipp' => 5])->setModel($files);
