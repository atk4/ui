<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsModal;
use Atk4\Ui\LoremIpsum;
use Atk4\Ui\Message;
use Atk4\Ui\Modal;
use Atk4\Ui\Table;
use Atk4\Ui\Text;
use Atk4\Ui\View;
use Atk4\Ui\VirtualPage;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demonstrate the use of VirtualPage

// define virtual page
$virtualPage = VirtualPage::addTo($app->layout, ['urlTrigger' => 'in']);

// add content to virtual page
if ($app->hasRequestQueryParam('p_id')) {
    Header::addTo($virtualPage, [$app->getRequestQueryParam('p_id')])->addClass('__atk-behat-test-car');
}
LoremIpsum::addTo($virtualPage, ['size' => 1]);
$virtualPageButton = Button::addTo($virtualPage, ['Back', 'icon' => 'left arrow']);
$virtualPageButton->link('virtual.php');
$virtualPage->ui = 'grey inverted segment';

$modal = Modal::addTo($virtualPage);
$modal->set(static function (View $p) {
    Text::addTo($p)->set('This is yet another modal');
    LoremIpsum::addTo($p, ['size' => 2]);
});
$button = Button::addTo($virtualPage)->set('Open Lorem Ipsum');
$button->on('click', $modal->jsShow());

$msg = Message::addTo($app, ['Virtual Page']);
$msg->text->addParagraph('Virtual page content are not rendered on page load. They will ouptput their content when trigger.');
$msg->text->addParagraph('Click button below to trigger it.');

// button that trigger virtual page
$button = Button::addTo($app, ['More info on Car']);
$button->link($virtualPage->cb->getUrl() . '&p_id=Car');

$button = Button::addTo($app, ['More info on Bike']);
$button->link($virtualPage->cb->getUrl() . '&p_id=Bike');

Header::addTo($app, ['Virtual Page Logic']);

$virtualPage = VirtualPage::addTo($app); // this page will not be visible unless you trigger it specifically
View::addTo($virtualPage, ['Contents of your pop-up here'])->addClass('ui header __atk-behat-test-content');
LoremIpsum::addTo($virtualPage, ['size' => 2]);

Counter::addTo($virtualPage);
View::addTo($virtualPage, ['ui' => 'hidden divider']);
Button::addTo($virtualPage, ['Back', 'icon' => 'left arrow'])->link('virtual.php');

$bar = View::addTo($app, ['ui' => 'buttons']);
Button::addTo($bar)->set('Inside current layout')->link($virtualPage->getUrl());
Button::addTo($bar)->set('On a blank page')->link($virtualPage->getUrl('popup'));
Button::addTo($bar)->set('No layout at all')->link($virtualPage->getUrl('cut'));

Header::addTo($app, ['Inside Modal', 'subHeader' => 'Virtual page content can be display using JsModal Class.']);

$bar = View::addTo($app, ['ui' => 'buttons']);
Button::addTo($bar)->set('Load in Modal')
    ->on('click', new JsModal('My Popup Title', $virtualPage->getJsUrl('cut')));

Button::addTo($bar)->set('Simulate slow load')
    ->on('click', new JsModal('My Popup Title', $virtualPage->getJsUrl('cut') . '&slow=true'));
if ($app->hasRequestQueryParam('slow')) {
    sleep(1);
}

Button::addTo($bar)->set('No title')
    ->on('click', new JsModal(null, $virtualPage->getJsUrl('cut')));

View::addTo($app, ['ui' => 'hidden divider']);
$text = Text::addTo($app);
$text->addParagraph('Can also be trigger from a JS event, like clicking on a table row.');
$table = Table::addTo($app, ['class.celled' => true]);
$table->setModel(new SomeData());

$frame = VirtualPage::addTo($app);
$frame->set(static function (VirtualPage $p) {
    Header::addTo($p, ['Clicked row with ID = ' . $p->getApp()->tryGetRequestQueryParam('id')]);
});

$table->onRowClick(new JsModal('Row Clicked', $frame, ['id' => $table->jsRow()->data('id')]));
