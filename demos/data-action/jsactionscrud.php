<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Crud;
use Atk4\Ui\Header;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Actions in Crud', 'subHeader' => 'Crud will automatically setup Menu items based on actions defined in model.']);

// Actions can be added easily to the model

$files = new File($app->db);

// this action must appear on top of the Crud
$files->addUserAction('import_from_filesystem', [
    'caption' => 'Import',
    'callback' => 'importFromFilesystem',
    'description' => 'Import file using path:',
    'preview' => static function (Model $model, $path) {
        return 'Execute Import using path: "' . $path . '"';
    },
    'args' => [
        'path' => ['type' => 'string', 'required' => true],
    ],
    'appliesTo' => Model\UserAction::APPLIES_TO_NO_RECORDS,
]);

$files->addUserAction('download', static function (Model $model) {
    return 'File has been download!';
});

Crud::addTo($app, ['ipp' => 10])
    ->setModel($files);
