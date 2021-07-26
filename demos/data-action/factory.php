<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\CardDeck;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Executor Factory in View Instance', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['factory-view']);
View::addTo($app, ['ui' => 'ui clearing divider']);

$msg = \Atk4\Ui\Message::addTo($app, [
    'Customizing action trigger by Overriding Executor Factory',
]);
$msg->text->addParagraph('');

$msg->text->addHtml('Override Executor class may be applied globally, via the App instance like below, or per <a href="factory-view.php">View instance</a>.');

$msg->text->addParagraph('In this example, Crud and Card button was changed and set through the App instance.');

// Overriding basic ExecutorFactory in order to change Table and Modal button.
// and also changing default add action label.
$myFactory = get_class(new class() extends ExecutorFactory {
    public const BUTTON_PRIMARY_COLOR = 'green';

    protected $triggerSeed = [
        self::TABLE_BUTTON => [
            'edit' => [Button::class, null, 'icon' => 'pencil'],
            'delete' => [Button::class, null, 'icon' => 'times red'],
        ],
        self::CARD_BUTTON => [
            'edit' => [Button::class, 'Edit', 'icon' => 'pencil', 'ui' => 'tiny button'],
            'delete' => [Button::class, 'Remove', 'icon' => 'times', 'ui' => 'tiny button'],
        ],
    ];

    protected $triggerCaption = [
        'add' => 'Add New Record',
    ];
});

// Set new executor factory globally.
$app->setExecutorFactory(new $myFactory());

$country = new CountryLock($app->db);

$crud = \Atk4\Ui\Crud::addTo($app, ['ipp' => 5]);
$crud->setModel($country);

View::addTo($app, ['class' => ['ui divider']]);

$deck = CardDeck::addTo($app, ['menu' => false, 'search' => false, 'paginator' => false, 'useTable' => true]);
$deck->setModel($country->setLimit(3), [$country->fieldName()->iso, $country->fieldName()->iso3]);
