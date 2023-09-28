<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Card;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Icon;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Message;
use Atk4\Ui\Panel;
use Atk4\Ui\Text;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$country = new Country($app->db);
DemoActionsUtil::setupDemoActions($country);

Header::addTo($app, ['Right Panel', 'subHeader' => 'Content on the fly!']);

// PANEL

Header::addTo($app, ['Static', 'size' => 4, 'subHeader' => 'Panel may have static content only.']);
$panel = Panel\Right::addTo($app, ['dynamic' => []]);
Message::addTo($panel, ['This panel contains only static content.']);
$button = Button::addTo($app, ['Open Static']);
$button->on('click', $panel->jsOpen());
View::addTo($app, ['ui' => 'divider']);

// PANEL_1

Header::addTo($app, ['Dynamic', 'size' => 4, 'subHeader' => 'Panel can load content dynamically']);
$panel1 = Panel\Right::addTo($app);

Message::addTo($panel1, ['This panel will load content dynamically below according to button select on the right.']);
$button = Button::addTo($app, ['Button 1']);
$button->js(true)->data('btn', '1');
$button->on('click', $panel1->jsOpen([], ['btn'], 'orange'));

$button = Button::addTo($app, ['Button 2']);
$button->js(true)->data('btn', '2');
$button->on('click', $panel1->jsOpen([], ['btn'], 'orange'));

$view = View::addTo($app, ['ui' => 'segment']);
$text = Text::addTo($view);
$text->set($app->tryGetRequestQueryParam('txt') ?? 'Not Complete');

$panel1->onOpen(static function (Panel\Content $p) use ($view) {
    $panel = View::addTo($p, ['ui' => 'basic segment']);
    $buttonNumber = $panel->stickyGet('btn');

    $panelText = 'You loaded panel content using button #' . $buttonNumber;
    Message::addTo($panel, ['Panel 1', 'text' => $panelText]);

    $reloadPanelButton = Button::addTo($panel, ['Reload Myself']);
    $reloadPanelButton->on('click', new JsReload($panel));

    View::addTo($panel, ['ui' => 'divider']);
    $panelButton = Button::addTo($panel, ['Complete']);
    $panelButton->on('click', new JsBlock([
        $p->getOwner()->jsClose(),
        new JsReload($view, ['txt' => 'Complete using button #' . $buttonNumber]),
    ]));
});

View::addTo($app, ['ui' => 'divider']);

// PANEL_2

Header::addTo($app, ['Closing option', 'size' => 4, 'subHeader' => 'Panel can prevent from closing.']);

$panel2 = Panel\Right::addTo($app, ['hasClickAway' => false]);
$icon = Icon::addTo($app, ['big cog'])->setStyle('cursor', 'pointer');
$icon->on('click', $panel2->jsOpen());
$panel2->addConfirmation('Changes will be lost. Are you sure?');

$msg = Message::addTo($panel2, ['Prevent close.']);

$txt = Text::addTo($msg);
$txt->addParagraph('This panel can only be closed via it\'s close icon at top right.');
$txt->addParagraph('Try to change dropdown value and close without saving!');

$panel2->onOpen(static function (Panel\Content $p) {
    $form = Form::addTo($p);
    $form->addHeader('Settings');
    $form->addControl('name', [Form\Control\Dropdown::class, 'values' => [1 => 'Option 1', 2 => 'Option 2']])
        ->set('1')
        ->onChange($p->getOwner()->jsDisplayWarning(true));

    $form->onSubmit(static function (Form $form) use ($p) {
        return new JsBlock([
            new JsToast('Saved, closing panel.'),
            $p->getOwner()->jsDisplayWarning(false),
            $p->getOwner()->jsClose(),
        ]);
    });
});
View::addTo($app, ['ui' => 'divider']);

// PANEL_3

Header::addTo($app, ['UserAction Friendly', 'size' => 4, 'subHeader' => 'Panel can run model action.']);

$panel3 = Panel\Right::addTo($app);
$countryId = $panel3->stickyGet('id');
$msg = Message::addTo($panel3, ['Run Country model action below.']);

$deck = View::addTo($app, ['ui' => 'cards']);
$country->setLimit(3);

foreach ($country as $ct) {
    $c = Card::addTo($deck, ['useLabel' => true])->setStyle('cursor', 'pointer');
    $c->setModel($ct);
    $c->on('click', $panel3->jsOpen([], ['id'], 'orange'));
}

$panel3->onOpen(static function (Panel\Content $p) use ($country, $countryId) {
    $seg = View::addTo($p, ['ui' => 'basic segment center aligned']);
    Header::addTo($seg, [$country->load($countryId)->getTitle()]);
    $buttons = View::addTo($seg, ['ui' => 'vertical basic buttons']);
    foreach ($country->getUserActions() as $action) {
        $button = Button::addTo($buttons, [$action->getCaption()]);
        $button->on('click', $action, ['args' => ['id' => $countryId]]);
    }
});
