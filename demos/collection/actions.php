<?php

require_once __DIR__ . '/../atk-init.php';

\atk4\ui\Button::addTo($app, ['js Event Executor', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['jsactions']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Extensions to ATK Data Actions', 'subHeader' => 'Demonstrate how to augment your models with actions and vizualize those inside UI']);

// Actions can be added easily to the model

$files = new FileLock($db);

// This action must appear on top of the CRUD
$action = $files->addAction(
    'import_from_filesystem',
    [
        'callback' => 'importFromFilesystem',
        'preview' => function ($model, $path) {
            return 'Considering path: ' . $path;
        },
        'args' => [
            'path' => ['type' => 'string', 'required' => true],
        ],
        'scope' => atk4\data\UserAction\Generic::NO_RECORDS,
    ]
);

$files->addAction('download', function ($m) {
    $len = mb_strlen(file_get_contents($m['name']));

    return "{$len} bytes downloaded..";
});

//$files->getAction('download')->system = true;

$app->add($grid = new \atk4\ui\GridLayout(['columns' => 3]));

$grid->add($executor = new \atk4\ui\ActionExecutor\Basic(), 'r1c1');
$executor->setAction($action);
$executor->ui = 'segment';
$executor->description = 'Execute action using "Basic" executor and path="." argument';
$executor->setArguments(['path' => '.']);
$executor->onHook('afterExecute', function ($x, $ret) {
    return new \atk4\ui\jsToast('Files imported: ' . $ret);
});

$grid->add($executor = new \atk4\ui\ActionExecutor\ArgumentForm(), 'r1c2');
$executor->setAction($action);
$executor->description = 'ArgumentForm executor will ask user about arguments';
$executor->ui = 'segment';
$executor->onHook('afterExecute', function ($x, $ret) {
    return new \atk4\ui\jsToast('Files imported: ' . $ret);
});

$grid->add($executor = new \atk4\ui\ActionExecutor\Preview(), 'r1c3');
$executor->setAction($action);
$executor->ui = 'segment';
$executor->previewType = 'console';
$executor->description = 'Displays preview in console prior to executing';
$executor->setArguments(['path' => '.']);
$executor->onHook('afterExecute', function ($x, $ret) {
    return new \atk4\ui\jsToast('Files imported: ' . $ret);
});

\atk4\ui\CRUD::addTo($app, ['ipp' => 5])->setModel($files);
