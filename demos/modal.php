<?php

require_once __DIR__ . '/init.php';
// Re-usable component implementing counter

\atk4\ui\Header::addTo($app, ['Static Modal Dialog']);

$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);

$modal = \atk4\ui\Modal::addTo($app, ['title' => 'Add a name']);
\atk4\ui\LoremIpsum::addTo($modal);
\atk4\ui\Button::addTo($modal, ['Hide'])->on('click', $modal->hide());

$noTitle = \atk4\ui\Modal::addTo($app, ['title' => false]);
\atk4\ui\LoremIpsum::addTo($noTitle);
\atk4\ui\Button::addTo($noTitle, ['Hide'])->on('click', $noTitle->hide());

$scrolling = \atk4\ui\Modal::addTo($app, ['title' => 'Long Content that Scrolls inside Modal']);
$scrolling->addScrolling();
\atk4\ui\LoremIpsum::addTo($scrolling);
\atk4\ui\LoremIpsum::addTo($scrolling);
\atk4\ui\LoremIpsum::addTo($scrolling);
\atk4\ui\Button::addTo($scrolling, ['Hide'])->on('click', $scrolling->hide());

\atk4\ui\Button::addTo($bar, ['Show'])->on('click', $modal->show());
\atk4\ui\Button::addTo($bar, ['No Title'])->on('click', $noTitle->show());
\atk4\ui\Button::addTo($bar, ['Scrolling Content'])->on('click', $scrolling->show());

if (!class_exists('Counter')) {
    class Counter extends \atk4\ui\FormField\Line
    {
        public $content = 20;

        public function init()
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

$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
\atk4\ui\Button::addTo($bar)->set('Inside current layout')->link($vp->getURL());
\atk4\ui\Button::addTo($bar)->set('On a blank page')->link($vp->getURL('popup'));
\atk4\ui\Button::addTo($bar)->set('No layout at all')->link($vp->getURL('cut'));

\atk4\ui\Header::addTo($app, ['Actual pop-ups']);

$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
\atk4\ui\Button::addTo($bar)->set('Open in Pop-up')->on('click', new \atk4\ui\jsExpression('window.open([], "", "width=800,height=500")', [$vp->getURL('popup')]));
\atk4\ui\Button::addTo($bar)->set('Load in Modal')->on('click', new \atk4\ui\jsModal('My Popup Title', $vp->getJSURL('cut')));

\atk4\ui\Button::addTo($bar)->set('Simulate slow load')->on('click', new \atk4\ui\jsModal('My Popup Title', $vp->getJSURL('cut') . '&slow=true'));
if (isset($_GET['slow'])) {
    sleep(1);
}

\atk4\ui\Button::addTo($bar)->set('No title')->on('click', new \atk4\ui\jsModal(null, $vp->getJSURL('cut') . '&slow=true'));
if (isset($_GET['slow'])) {
    sleep(1);
}

\atk4\ui\Header::addTo($app, ['Modal when you click on table row']);
$t = \atk4\ui\Table::addTo($app, ['celled' => true]);
$t->setModel(new SomeData());

$frame = \atk4\ui\VirtualPage::addTo($app);
$frame->set(function ($frame) {
    \atk4\ui\Header::addTo($frame, ['Clicked row with ID = ' . $_GET['id']]);
});

$t->onRowClick(new \atk4\ui\jsModal('Row Clicked', $frame, ['id' => $t->jsRow()->data('id')]));
