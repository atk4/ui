<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Columns;
use Atk4\Ui\Header;
use Atk4\Ui\UserAction;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$files = new FileLock($app->db);

// Actions can be added easily to the model via the Model::addUserAction($name, $properties) method.
$action = $files->addUserAction(
    'import_from_filesystem',
    [
        // Which fields may be edited for the action. Default to all fields.
        // ModalExecutor for example, will only display fields set in this array.
        'fields' => ['name'],
        // callback function to call in model when action execute.
        // Can use a closure function or model method.
        'callback' => 'importFromFilesystem',
        // Some Ui action executor will use this property for displaying text in button.
        // Can be override by some Ui executor description property.
        'description' => 'Import file in a specify path.',
        // Display information prior to execute the action.
        // ModalExecutor or PreviewExecutor will display preview.
        'preview' => function ($model, $path) {
            return 'Execute Import using path: "' . $path . '"';
        },
        // Argument needed to run the callback action method.
        // Some ui executor will ask for arguments prior to run the action, like the ModalExecutor.
        'args' => [
            'path' => ['type' => 'string', 'required' => true],
        ],
        'appliesTo' => \Atk4\Data\Model\UserAction::APPLIES_TO_NO_RECORDS,
    ]
);

Header::addTo($app, [
    'Extensions to ATK Data Actions',
    'subHeader' => 'Showing different UserAction executors that can execute Atk4\Data model action.',
]);

View::addTo($app, ['ui' => 'hidden divider']);

$columns = Columns::addTo($app, ['width' => 2]);
$rightColumn = $columns->addColumn();
$leftColumn = $columns->addColumn();

Header::addTo($rightColumn, [
    'JsCallbackExecutor',
    'subHeader' => 'Path argument is set via POST url when setting actions in executor.',
]);
// Explicitly adding an Action executor.
$executor = UserAction\JsCallbackExecutor::addTo($rightColumn);
// Passing Model action to executor and action argument via url.
$executor->setAction($action, ['path' => '.']);
// Setting user response after model action get execute.
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($t, $m) {
    return new \Atk4\Ui\JsToast('Files imported');
});

$btn = \Atk4\Ui\Button::addTo($rightColumn, ['Import File']);
$btn->on('click', $executor, ['confirm' => 'This will import a lot of file. Are you sure?']);

Header::addTo($rightColumn, ['BasicExecutor']);
$executor = UserAction\BasicExecutor::addTo($rightColumn, ['executorButton' => [Button::class, 'Import', 'primary']]);
$executor->setAction($action);
$executor->ui = 'segment';
$executor->description = 'Execute Import action using "BasicExecutor" with argument "path" equal to "."';
$executor->setArguments(['path' => '.']);
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($x) {
    return new \Atk4\Ui\JsToast('Done!');
});

View::addTo($rightColumn, ['ui' => 'hidden divider']);

Header::addTo($rightColumn, ['PreviewExecutor']);
$executor = UserAction\PreviewExecutor::addTo($rightColumn);
$executor->setAction($action);
$executor->ui = 'segment';
$executor->previewType = 'console';
$executor->description = 'Displays preview in console prior to executing';
$executor->setArguments(['path' => '.']);
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($x, $ret) {
    return new \Atk4\Ui\JsToast('Confirm!');
});

Header::addTo($leftColumn, ['FormExecutor']);
$executor = UserAction\FormExecutor::addTo($leftColumn, ['executorButton' => [Button::class, 'Save Name Only', 'primary']]);
$executor->setAction($action);
$executor->ui = 'segment';
$executor->description = 'Only fields set in $action[field] array will be added in form.';
$executor->setArguments(['path' => '.']);
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($x, $ret) {
    return new \Atk4\Ui\JsToast('Confirm! ' . $x->action->getModel()->get('name'));
});

View::addTo($leftColumn, ['ui' => 'hidden divider']);

Header::addTo($leftColumn, ['ArgumentFormExecutor']);
$executor = UserAction\ArgumentFormExecutor::addTo($leftColumn, ['executorButton' => [Button::class, 'Run Import', 'primary']]);
$executor->setAction($action);
$executor->description = 'ArgumentFormExecutor will ask user about arguments set in actions.';
$executor->ui = 'segment';
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($x, $ret) {
    return new \Atk4\Ui\JsToast('Imported!');
});
