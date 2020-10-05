<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demonstrate the use of Virtual Page.

// define virtual page.
$virtualPage = \atk4\ui\VirtualPage::addTo($app->layout, ['urlTrigger' => 'in']);

// Add content to virtual page.
if (isset($_GET['p_id'])) {
    \atk4\ui\Header::addTo($virtualPage, [$_GET['p_id']]);
}
\atk4\ui\LoremIpsum::addTo($virtualPage, ['size' => 1]);
$virtualPageButton = \atk4\ui\Button::addTo($virtualPage, ['Back', 'icon' => 'left arrow']);
$virtualPageButton->link('virtual.php');
$virtualPage->ui = 'grey inverted segment';

$msg = \atk4\ui\Message::addTo($app, ['Virtual Page']);
$msg->text->addParagraph('Virtual page content are not rendered on page load. They will ouptput their content when trigger.');
$msg->text->addParagraph('Click button below to trigger it.');

// button that trigger virtual page.
$btn = \atk4\ui\Button::addTo($app, ['More info on Car']);
$btn->link($virtualPage->cb->getUrl() . '&p_id=Car');

$btn = \atk4\ui\Button::addTo($app, ['More info on Bike']);
$btn->link($virtualPage->cb->getUrl() . '&p_id=Bike');

// Test 1 - Basic reloading
\atk4\ui\Header::addTo($app, ['Virtual Page Logic']);

$virtualPage = \atk4\ui\VirtualPage::addTo($app); // this page will not be visible unless you trigger it specifically
\atk4\ui\Header::addTo($virtualPage, ['Contens of your pop-up here']);
\atk4\ui\LoremIpsum::addTo($virtualPage, ['size' => 2]);

Counter::addTo($virtualPage);
\atk4\ui\View::addTo($virtualPage, ['ui' => 'hidden divider']);
\atk4\ui\Button::addTo($virtualPage, ['Back', 'icon' => 'left arrow'])->link('virtual.php');

$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
\atk4\ui\Button::addTo($bar)->set('Inside current layout')->link($virtualPage->getUrl());
\atk4\ui\Button::addTo($bar)->set('On a blank page')->link($virtualPage->getUrl('popup'));
\atk4\ui\Button::addTo($bar)->set('No layout at all')->link($virtualPage->getUrl('cut'));

\atk4\ui\Header::addTo($app, ['Inside Modal', 'subHeader' => 'Virtual page content can be display using JsModal Class.']);

$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
\atk4\ui\Button::addTo($bar)->set('Load in Modal')->on('click', new \atk4\ui\JsModal('My Popup Title', $virtualPage->getJsUrl('cut')));

\atk4\ui\Button::addTo($bar)->set('Simulate slow load')->on('click', new \atk4\ui\JsModal('My Popup Title', $virtualPage->getJsUrl('cut') . '&slow=true'));
if (isset($_GET['slow'])) {
    sleep(1);
}

\atk4\ui\Button::addTo($bar)->set('No title')->on('click', new \atk4\ui\JsModal(null, $virtualPage->getJsUrl('cut')));

\atk4\ui\View::addTo($app, ['ui' => 'hidden divider']);
$text = \atk4\ui\Text::addTo($app);
$text->addParagraph('Can also be trigger from a js event, like clicking on a table row.');
$table = \atk4\ui\Table::addTo($app, ['celled' => true]);
$table->setModel(new SomeData());

$frame = \atk4\ui\VirtualPage::addTo($app);
$frame->set(function ($frame) {
    \atk4\ui\Header::addTo($frame, ['Clicked row with ID = ' . $_GET['id']]);
});

$table->onRowClick(new \atk4\ui\JsModal('Row Clicked', $frame, ['id' => $table->jsRow()->data('id')]));
