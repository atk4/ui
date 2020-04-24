<?php

/*
 * Demonstrate the use of Virtual Page.
 */

chdir('..');
require_once 'atk-init.php';

// define virtual page.
$vp = \atk4\ui\VirtualPage::addTo($layout);
$vp->cb->urlTrigger = 'in';

// Add content to virtual page.
if (isset($_GET['p_id'])) {
    \atk4\ui\Header::addTo($vp, [$_GET['p_id']]);
}
\atk4\ui\LoremIpsum::addTo($vp, ['size' => 1]);
$vp_btn = \atk4\ui\Button::addTo($vp, ['Back', 'icon' => 'left arrow']);
$vp_btn->link('virtual.php');
$vp->ui = 'grey inverted segment';

$msg = \atk4\ui\Message::addTo($app, ['Virtual Page']);
$msg->text->addParagraph('Virtual page content are not rendered on page load. They will ouptput their content when trigger.');
$msg->text->addParagraph('Click button below to trigger it.');

// button that trigger virtual page.
$btn = \atk4\ui\Button::addTo($app, ['More info on Car']);
$btn->link($vp->cb->getUrl() . '&p_id=Car');

$btn = \atk4\ui\Button::addTo($app, ['More info on Bike']);
$btn->link($vp->cb->getUrl() . '&p_id=Bike');

if (!class_exists('Counter')) {
    class Counter extends \atk4\ui\FormField\Line
    {
        public $content = 20;

        public function init(): void
        {
            parent::init();

            $this->actionLeft = new \atk4\ui\Button(['icon' => 'minus']);
            $this->action = new \atk4\ui\Button(['icon' => 'plus']);

            $this->actionLeft->js('click', $this->jsInput()->val(new \atk4\ui\jsExpression('parseInt([])-1', [$this->jsInput()->val()])));
            $this->action->js('click', $this->jsInput()->val(new \atk4\ui\jsExpression('parseInt([])+1', [$this->jsInput()->val()])));
        }
    }
}

// Test 1 - Basic reloading
\atk4\ui\Header::addTo($app, ['Virtual Page Logic']);

$vp = \atk4\ui\VirtualPage::addTo($app); // this page will not be visible unless you trigger it specifically
\atk4\ui\Header::addTo($vp, ['Contens of your pop-up here']);
\atk4\ui\LoremIpsum::addTo($vp, ['size' => 2]);
Counter::addTo($vp);
\atk4\ui\View::addTo($vp, ['ui' => 'hidden divider']);
\atk4\ui\Button::addTo($vp, ['Back', 'icon' => 'left arrow'])->link('virtual.php');

$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
\atk4\ui\Button::addTo($bar)->set('Inside current layout')->link($vp->getURL());
\atk4\ui\Button::addTo($bar)->set('On a blank page')->link($vp->getURL('popup'));
\atk4\ui\Button::addTo($bar)->set('No layout at all')->link($vp->getURL('cut'));

\atk4\ui\Header::addTo($app, ['Inside Modal', 'subHeader' => 'Virtual page content can be display using jsModal Class.']);

$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
\atk4\ui\Button::addTo($bar)->set('Load in Modal')->on('click', new \atk4\ui\jsModal('My Popup Title', $vp->getJSURL('cut')));

\atk4\ui\Button::addTo($bar)->set('Simulate slow load')->on('click', new \atk4\ui\jsModal('My Popup Title', $vp->getJSURL('cut') . '&slow=true'));
if (isset($_GET['slow'])) {
    sleep(1);
}

\atk4\ui\Button::addTo($bar)->set('No title')->on('click', new \atk4\ui\jsModal(null, $vp->getJSURL('cut')));

\atk4\ui\View::addTo($app, ['ui' => 'hidden divider']);
$text = \atk4\ui\Text::addTo($app);
$text->addParagraph('Can also be trigger from a js event, like clicking on a table row.');
$t = \atk4\ui\Table::addTo($app, ['celled' => true]);
$t->setModel(new SomeData());

$frame = \atk4\ui\VirtualPage::addTo($app);
$frame->set(function ($frame) {
    \atk4\ui\Header::addTo($frame, ['Clicked row with ID = ' . $_GET['id']]);
});

$t->onRowClick(new \atk4\ui\jsModal('Row Clicked', $frame, ['id' => $t->jsRow()->data('id')]));
