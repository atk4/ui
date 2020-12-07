<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$country = new CountryLock($app->db);
DemoActionsUtil::setupDemoActions($country);

\Atk4\Ui\Header::addTo($app, ['Right Panel', 'subHeader' => 'Content on the fly!']);

// PANEL

\Atk4\Ui\Header::addTo($app, ['Static', 'size' => 4, 'subHeader' => 'Panel may have static content only.']);
$panel = $app->layout->addRightPanel(new \Atk4\Ui\Panel\Right(['dynamic' => false]));
\Atk4\Ui\Message::addTo($panel, ['This panel contains only static content.']);
$btn = \Atk4\Ui\Button::addTo($app, ['Open Static']);
$btn->on('click', $panel->jsOpen());
\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);

// PANEL_1

\Atk4\Ui\Header::addTo($app, ['Dynamic', 'size' => 4, 'subHeader' => 'Panel can load content dynamically']);
$panel1 = $app->layout->addRightPanel(new \Atk4\Ui\Panel\Right());
\Atk4\Ui\Message::addTo($panel1, ['This panel will load content dynamically below according to button select on the right.']);
$btn = \Atk4\Ui\Button::addTo($app, ['Button 1']);
$btn->js(true)->data('btn', '1');
$btn->on('click', $panel1->jsOpen(['btn'], 'orange'));

$btn = \Atk4\Ui\Button::addTo($app, ['Button 2']);
$btn->js(true)->data('btn', '2');
$btn->on('click', $panel1->jsOpen(['btn'], 'orange'));

$view = \Atk4\Ui\View::addTo($app, ['ui' => 'segment']);
$text = \Atk4\Ui\Text::addTo($view);
$text->set($_GET['txt'] ?? 'Not Complete');

$panel1->onOpen(function ($p) use ($view) {
    $panel = \Atk4\Ui\View::addTo($p, ['ui' => 'basic segment']);
    $buttonNumber = $panel->stickyGet('btn');

    $panelText = 'You loaded panel content using button #' . $buttonNumber;
    \Atk4\Ui\Message::addTo($panel, ['Panel 1', 'text' => $panelText]);

    $reloadPanelButton = \Atk4\Ui\Button::addTo($panel, ['Reload Myself']);
    $reloadPanelButton->on('click', new \Atk4\Ui\JsReload($panel));

    \Atk4\Ui\View::addTo($panel, ['ui' => 'divider']);
    $panelButton = \Atk4\Ui\Button::addTo($panel, ['Complete']);
    $panelButton->on('click', [
        $p->getOwner()->jsClose(),
        new \Atk4\Ui\JsReload($view, ['txt' => 'Complete using button #' . $buttonNumber]),
    ]);
});

\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);

// PANEL_2

\Atk4\Ui\Header::addTo($app, ['Closing option', 'size' => 4, 'subHeader' => 'Panel can prevent from closing.']);

$panel2 = $app->layout->addRightPanel(new \Atk4\Ui\Panel\Right(['hasClickAway' => false]));
$icon = \Atk4\Ui\Icon::addTo($app, ['big cog'])->addStyle('cursor', 'pointer');
$icon->on('click', $panel2->jsOpen());
$panel2->addConfirmation('Changes will be lost. Are you sure?');

$msg = \Atk4\Ui\Message::addTo($panel2, ['Prevent close.']);

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
\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);

// PANEL_3

$countryId = $app->stickyGet('id');
\Atk4\Ui\Header::addTo($app, ['UserAction Friendly', 'size' => 4, 'subHeader' => 'Panel can run model action.']);
$panel3 = $app->layout->addRightPanel(new \Atk4\Ui\Panel\Right());
$msg = \Atk4\Ui\Message::addTo($panel3, ['Run Country model action below.']);

$deck = \Atk4\Ui\View::addTo($app, ['ui' => 'cards']);
$country->setLimit(3);

foreach ($country as $ct) {
    $c = \Atk4\Ui\Card::addTo($deck, ['useLabel' => true])->addStyle('cursor', 'pointer');
    $c->setModel($ct);
    $c->on('click', $panel3->jsOpen(['id'], 'orange'));
}

$panel3->onOpen(function ($p) use ($country, $countryId) {
    $seg = \Atk4\Ui\View::addTo($p, ['ui' => 'basic segment center aligned']);
    \Atk4\Ui\Header::addTo($seg, [$country->load($countryId)->getTitle()]);
    $buttons = \Atk4\Ui\View::addTo($seg, ['ui' => 'vertical basic buttons']);
    foreach ($country->getUserActions() as $action) {
        $button = \Atk4\Ui\Button::addTo($buttons, [$action->getCaption()]);
        $button->on('click', $action, ['args' => ['id' => $countryId]]);
    }
});
