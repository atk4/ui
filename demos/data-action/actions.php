<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Button;
use atk4\ui\Columns;
use atk4\ui\Header;
use atk4\ui\UserAction;
use atk4\ui\View;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Actions can be added easily to the model

$files = new FileLock($app->db);

// This action must appear on top of the Crud
$action = $files->addUserAction(
    'import_from_filesystem',
    [
        'callback' => 'importFromFilesystem',
        'description' => 'Import',
        'preview' => function ($model, $path) {
            return 'Execute Import using path: "' . $path . '"';
        },
        'args' => [
            'path' => ['type' => 'string', 'required' => true],
        ],
        'appliesTo' => \atk4\data\Model\UserAction::APPLIES_TO_NO_RECORDS,
    ]
);

Header::addTo($app, [
    'Extensions to ATK Data Actions',
    'subHeader' => 'Showing Ui UserAction executor that can execute atk4\data model action.',
]);

View::addTo($app, ['ui' => 'hidden divider']);

Header::addTo($app, ['Executing an action with a JsCallbackExecutor', 'subHeader' => 'Button is set in order to ask for a confirmation.']);
// Explicitly adding an Action executor.
$executor = UserAction\JsCallbackExecutor::addTo($app);
// Passing Model action to executor and action argument via url.
$executor->setAction($action, ['path' => '.']);
// Setting user response after model action get execute.
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($t, $m) {
    return new \atk4\ui\JsToast('Files imported');
});

$btn = \atk4\ui\Button::addTo($app, ['Import File']);
$btn->on('click', $executor, ['confirm' => 'This will import a lot of file. Are you sure?']);

View::addTo($app, ['ui' => 'hidden divider']);

$columns = Columns::addTo($app, ['width' => 2]);
$rightColumn = $columns->addColumn();

Header::addTo($rightColumn, ['Executing an action with a BasicExecutor']);
$executor = UserAction\BasicExecutor::addTo($rightColumn, ['executorButton' => [Button::class, 'Import', 'primary']]);
$executor->setAction($action);
$executor->ui = 'segment';
$executor->description = 'Execute Import action using "BasicExecutor" and argument path equal to "."';
$executor->setArguments(['path' => '.']);
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($x) {
    return new \atk4\ui\JsToast('Done!');
});

View::addTo($rightColumn, ['ui' => 'hidden divider']);

Header::addTo($rightColumn, ['Executing an action with a FormExecutor']);
$executor = UserAction\ArgumentFormExecutor::addTo($rightColumn);
$executor->setAction($action);
$action->description = 'Run Import';
$executor->description = 'ArgumentFormExecutor will ask user about arguments set in actions.';
$executor->ui = 'segment';
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($x, $ret) {
    return new \atk4\ui\JsToast('Imported!');
});

View::addTo($rightColumn, ['ui' => 'hidden divider']);

Header::addTo($rightColumn, ['Executing an action with a PreviewExecutor']);
$executor = UserAction\PreviewExecutor::addTo($rightColumn);
$executor->setAction($action);
$executor->ui = 'segment';
$executor->previewType = 'console';
$executor->description = 'Displays preview in console prior to executing';
$executor->setArguments(['path' => '.']);
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($x, $ret) {
    return new \atk4\ui\JsToast('Confirm!');
});
