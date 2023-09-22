<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Card;
use Atk4\Ui\Header;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Executor Factory in App instance', 'class.small left floated basic blue' => true, 'icon' => 'left arrow'])
    ->link(['factory']);
View::addTo($app, ['ui' => 'clearing divider']);

// overriding basic ExecutorFactory in order to change Card button
$myFactory = AnonymousClassNameCache::get_class(fn () => new class() extends ExecutorFactory {
    public $buttonPrimaryColor = 'green';

    protected array $actionIcon = [
        'callback' => 'sync',
        'preview' => 'eye',
        'edit_argument' => 'user edit',
        'edit_argument_preview' => 'pen square',
        'edit_iso' => 'pencil',
        'confirm' => 'check circle',
        'multi_step' => 'window maximize outline',
    ];

    public function __construct()
    {
        // registering card button default with our own method handler
        $this->triggerSeed = array_merge(
            $this->triggerSeed,
            [self::CARD_BUTTON => ['default' => [$this, 'getCardButton']]]
        );
    }

    /**
     * @return array
     */
    protected function getCardButton(Model\UserAction $action)
    {
        return [Button::class, 'icon' => $this->actionIcon[$action->shortName]];
    }
});

Header::addTo($app, ['Executor Factory set for this Card View only.']);

$country = new Country($app->db);
DemoActionsUtil::setupDemoActions($country);
$country = $country->loadBy($country->fieldName()->iso, 'fr');
$country->name .= ' NO RELOAD';
// suppress dirty field exception
// https://github.com/atk4/data/blob/35dd7b7d95909cfe574b15e32b7cc57c39a16a58/src/Model/UserAction.php#L164
unset($country->getDirtyRef()[$country->fieldName()->name]);

$cardActions = Card::addTo($app, ['useLabel' => true, 'executorFactory' => new $myFactory()]);
$cardActions->setModel($country);
foreach ($country->getModel()->getUserActions() as $action) {
    $showActions = ['callback', 'preview', 'edit_argument', 'edit_argument_preview', 'edit_iso', 'confirm', 'multi_step'];
    if (in_array($action->shortName, $showActions, true)) {
        $cardActions->addClickAction($action);
    }
}

// -----------------------------------------------------------------------------

Header::addTo($app, ['Card View using global Executor Factory']);

$country = new Country($app->db);
$country = $country->loadBy($country->fieldName()->iso, 'cz');
$country->name .= ' NO RELOAD';

$card = Card::addTo($app, ['useLabel' => true]);
$card->setModel($country);
$card->addClickAction($country->getUserAction('edit'));
$card->addClickAction($country->getUserAction('delete'));
