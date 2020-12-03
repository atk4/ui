<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\CardDeck;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$msg = \Atk4\Ui\Message::addTo($app, [
    'Overriding Executor Factory',
]);
$msg->text->addParagraph('You may easily 
change the look of model user action trigger element by overriding the
Executor factory class.
');
$msg->text->addParagraph('Override Executor class may be applied globally to the application or per View instance.');

// Overriding basic ExecutorFactory in order to change Table and Modal button.
// and also changing default add action label.
$myFactory = get_class(new class() extends ExecutorFactory {
    protected static $actionTriggerSeed = [
        self::MODAL_BUTTON => [
            'edit' => [Button::class, 'Save', 'green'],
            'add' => [Button::class, 'Save', 'green'],
        ],
        self::TABLE_BUTTON => [
            'edit' => [Button::class, null, 'icon' => 'pencil'],
            'delete' => [Button::class, null, 'icon' => 'times red'],
        ],
        self::CARD_BUTTON => [
            'edit' => [Button::class, 'Edit', 'icon' => 'pencil', 'ui' => 'tiny button'],
            'delete' => [Button::class, 'Remove', 'icon' => 'times', 'ui' => 'tiny button'],
        ],
    ];

    protected static $actionCaption = [
        'add' => 'Add New Record',
    ];
});

// Set new executor factory globally.
$app->defaultExecutorFactory = $myFactory;

$model = new CountryLock($app->db);

$crud = \Atk4\Ui\Crud::addTo($app, ['ipp' => 5]);
$crud->setModel($model);

View::addTo($app, ['class' => ['ui divider']]);

$deck = CardDeck::addTo($app, ['menu' => false, 'search' => false, 'paginator' => false, 'useTable' => true]);
$deck->setModel($model->setLimit(3), ['iso', 'iso3']);
