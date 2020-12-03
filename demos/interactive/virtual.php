<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demonstrate the use of Virtual Page.

// define virtual page.
$virtualPage = \Atk4\Ui\VirtualPage::addTo($app->layout, ['urlTrigger' => 'in']);

// Add content to virtual page.
if (isset($_GET['p_id'])) {
    \Atk4\Ui\Header::addTo($virtualPage, [$_GET['p_id']]);
}
\Atk4\Ui\LoremIpsum::addTo($virtualPage, ['size' => 1]);
$virtualPageButton = \Atk4\Ui\Button::addTo($virtualPage, ['Back', 'icon' => 'left arrow']);
$virtualPageButton->link('virtual.php');
$virtualPage->ui = 'grey inverted segment';

$msg = \Atk4\Ui\Message::addTo($app, ['Virtual Page']);
$msg->text->addParagraph('Virtual page content are not rendered on page load. They will ouptput their content when trigger.');
$msg->text->addParagraph('Click button below to trigger it.');

// button that trigger virtual page.
$btn = \Atk4\Ui\Button::addTo($app, ['More info on Car']);
$btn->link($virtualPage->cb->getUrl() . '&p_id=Car');

$btn = \Atk4\Ui\Button::addTo($app, ['More info on Bike']);
$btn->link($virtualPage->cb->getUrl() . '&p_id=Bike');

// Test 1 - Basic reloading
\Atk4\Ui\Header::addTo($app, ['Virtual Page Logic']);

$virtualPage = \Atk4\Ui\VirtualPage::addTo($app); // this page will not be visible unless you trigger it specifically
\Atk4\Ui\Header::addTo($virtualPage, ['Contens of your pop-up here']);
\Atk4\Ui\LoremIpsum::addTo($virtualPage, ['size' => 2]);

Counter::addTo($virtualPage);
\Atk4\Ui\View::addTo($virtualPage, ['ui' => 'hidden divider']);
\Atk4\Ui\Button::addTo($virtualPage, ['Back', 'icon' => 'left arrow'])->link('virtual.php');

$bar = \Atk4\Ui\View::addTo($app, ['ui' => 'buttons']);
\Atk4\Ui\Button::addTo($bar)->set('Inside current layout')->link($virtualPage->getUrl());
\Atk4\Ui\Button::addTo($bar)->set('On a blank page')->link($virtualPage->getUrl('popup'));
\Atk4\Ui\Button::addTo($bar)->set('No layout at all')->link($virtualPage->getUrl('cut'));

\Atk4\Ui\Header::addTo($app, ['Inside Modal', 'subHeader' => 'Virtual page content can be display using JsModal Class.']);

$bar = \Atk4\Ui\View::addTo($app, ['ui' => 'buttons']);
\Atk4\Ui\Button::addTo($bar)->set('Load in Modal')->on('click', new \Atk4\Ui\JsModal('My Popup Title', $virtualPage->getJsUrl('cut')));

\Atk4\Ui\Button::addTo($bar)->set('Simulate slow load')->on('click', new \Atk4\Ui\JsModal('My Popup Title', $virtualPage->getJsUrl('cut') . '&slow=true'));
if (isset($_GET['slow'])) {
    sleep(1);
}

\Atk4\Ui\Button::addTo($bar)->set('No title')->on('click', new \Atk4\Ui\JsModal(null, $virtualPage->getJsUrl('cut')));

\Atk4\Ui\View::addTo($app, ['ui' => 'hidden divider']);
$text = \Atk4\Ui\Text::addTo($app);
$text->addParagraph('Can also be trigger from a js event, like clicking on a table row.');
$table = \Atk4\Ui\Table::addTo($app, ['celled' => true]);
$table->setModel(new SomeData());

$frame = \Atk4\Ui\VirtualPage::addTo($app);
$frame->set(function ($frame) {
    \Atk4\Ui\Header::addTo($frame, ['Clicked row with ID = ' . $_GET['id']]);
});

$table->onRowClick(new \Atk4\Ui\JsModal('Row Clicked', $frame, ['id' => $table->jsRow()->data('id')]));
