<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Header;
use Atk4\Ui\Message;
use Atk4\Ui\Panel\Right;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$country = new CountryLock($app->db);
DemoActionsUtil::setupDemoActions($country);

Header::addTo($app, ['Right Panel', 'subHeader' => 'Content on the fly!']);

// PANEL

Header::addTo($app, ['Static', 'size' => 4, 'subHeader' => 'Panel may have static content only.']);
$panel = Right::addTo($app, ['dynamic' => false]);
Message::addTo($panel, ['This panel contains only static content.']);
$btn = \Atk4\Ui\Button::addTo($app, ['Open Static']);
$btn->on('click', $panel->jsOpen());
View::addTo($app, ['ui' => 'divider']);

// PANEL_1

Header::addTo($app, ['Dynamic', 'size' => 4, 'subHeader' => 'Panel can load content dynamically']);
$panel1 = Right::addTo($app);

Message::addTo($panel1, ['This panel will load content dynamically below according to button select on the right.']);
$btn = \Atk4\Ui\Button::addTo($app, ['Button 1']);
$btn->js(true)->data('btn', '1');
$btn->on('click', $panel1->jsOpen([], ['btn'], 'orange'));

$btn = \Atk4\Ui\Button::addTo($app, ['Button 2']);
$btn->js(true)->data('btn', '2');
$btn->on('click', $panel1->jsOpen([], ['btn'], 'orange'));

$view = View::addTo($app, ['ui' => 'segment']);
$text = \Atk4\Ui\Text::addTo($view);
$text->set($_GET['txt'] ?? 'Not Complete');

$panel1->onOpen(function ($p) use ($view) {
    $panel = View::addTo($p, ['ui' => 'basic segment']);
    $buttonNumber = $panel->stickyGet('btn');

    $panelText = 'You loaded panel content using button #' . $buttonNumber;
    Message::addTo($panel, ['Panel 1', 'text' => $panelText]);

    $reloadPanelButton = \Atk4\Ui\Button::addTo($panel, ['Reload Myself']);
    $reloadPanelButton->on('click', new \Atk4\Ui\JsReload($panel));

    View::addTo($panel, ['ui' => 'divider']);
    $panelButton = \Atk4\Ui\Button::addTo($panel, ['Complete']);
    $panelButton->on('click', [
        $p->getOwner()->jsClose(),
        new \Atk4\Ui\JsReload($view, ['txt' => 'Complete using button #' . $buttonNumber]),
    ]);
});

View::addTo($app, ['ui' => 'divider']);

// PANEL_2

Header::addTo($app, ['Closing option', 'size' => 4, 'subHeader' => 'Panel can prevent from closing.']);

$panel2 = Right::addTo($app, ['hasClickAway' => false]);
$icon = \Atk4\Ui\Icon::addTo($app, ['big cog'])->addStyle('cursor', 'pointer');
$icon->on('click', $panel2->jsOpen());
$panel2->addConfirmation('Changes will be lost. Are you sure?');

$msg = Message::addTo($panel2, ['Prevent close.']);

$txt = \Atk4\Ui\Text::addTo($msg);
$txt->addParagraph('This panel can only be closed via it\'s close icon at top right.');
$txt->addParagraph('Try to change dropdown value and close without saving!');

$panel2->onOpen(function ($p) {
    $form = \Atk4\Ui\Form::addTo($p);
    $form->addHeader('Settings');
    $form->addControl('name', [\Atk4\Ui\Form\Control\Dropdown::class, 'values' => ['1' => 'Option 1', '2' => 'Option 2']])
        ->set('1')
        ->onChange($p->getOwner()->jsDisplayWarning(true));

    $form->onSubmit(function (\Atk4\Ui\Form $form) use ($p) {
        return [
            new \Atk4\Ui\JsToast('Saved, closing panel.'),
            $p->getOwner()->jsDisplayWarning(false),
            $p->getOwner()->jsClose(),
        ];
    });
});
View::addTo($app, ['ui' => 'divider']);

// PANEL_3

$countryId = $app->stickyGet('id');
Header::addTo($app, ['UserAction Friendly', 'size' => 4, 'subHeader' => 'Panel can run model action.']);

$panel3 = Right::addTo($app);
$msg = Message::addTo($panel3, ['Run Country model action below.']);

$deck = View::addTo($app, ['ui' => 'cards']);
$country->setLimit(3);

foreach ($country as $ct) {
    $c = \Atk4\Ui\Card::addTo($deck, ['useLabel' => true])->addStyle('cursor', 'pointer');
    $c->setModel($ct);
    $c->on('click', $panel3->jsOpen([], ['id'], 'orange'));
}

$panel3->onOpen(function ($p) use ($country, $countryId) {
    $seg = View::addTo($p, ['ui' => 'basic segment center aligned']);
    Header::addTo($seg, [$country->load($countryId)->getTitle()]);
    $buttons = View::addTo($seg, ['ui' => 'vertical basic buttons']);
    foreach ($country->getUserActions() as $action) {
        $button = \Atk4\Ui\Button::addTo($buttons, [$action->getCaption()]);
        $button->on('click', $action, ['args' => ['id' => $countryId]]);
    }
});
