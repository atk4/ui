<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Card;
use Atk4\Ui\Header;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Executor Factory in App instance', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['factory']);
View::addTo($app, ['ui' => 'ui clearing divider']);

// Overriding basic ExecutorFactory in order to change Card button.
$myFactory = AnonymousClassNameCache::get_class(fn () => new class() extends ExecutorFactory {
    public const BUTTON_PRIMARY_COLOR = 'green';

    protected $actionIcon = [
        'callback' => 'sync',
        'preview' => 'eye',
        'edit_argument' => 'user edit',
        'edit_argument_prev' => 'pen square',
        'edit_iso' => 'pencil',
        'confirm' => 'check circle',
        'multi_step' => 'window maximize outline',
    ];

    public function __construct()
    {
        // registering card button default with our own method handler.
        $this->triggerSeed = array_merge(
            $this->triggerSeed,
            [self::CARD_BUTTON => ['default' => [$this, 'getCardButton']]]
        );
    }

    protected function getCardButton($action, $type)
    {
        return [Button::class, 'icon' => $this->actionIcon[$action->short_name]];
    }
});

Header::addTo($app, ['Executor Factory set for this Card View only.']);

DemoActionsUtil::setupDemoActions($country = new CountryLock($app->db));
$country = $country->loadAny();

$cardActions = Card::addTo($app, ['useLabel' => true, 'executorFactory' => new $myFactory()]);
$cardActions->setModel($country);
foreach ($country->getUserActions() as $action) {
    $showActions = ['callback', 'preview', 'edit_argument', 'edit_argument_prev', 'edit_iso', 'confirm', 'multi_step'];
    if (in_array($action->short_name, $showActions, true)) {
        $cardActions->addClickAction($action);
    }
}

////////////////////////

Header::addTo($app, ['Card View using global Executor Factory']);

$model = new CountryLock($app->db);
$model = $model->loadAny();

$card = Card::addTo($app, ['useLabel' => true]);
$card->setModel($model);
$card->addClickAction($model->getUserAction('edit'));
$card->addClickAction($model->getUserAction('delete'));
