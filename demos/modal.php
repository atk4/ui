<?php

require 'init.php';
// Re-usable component implementing counter
if (!class_exists('Counter')) {
class Counter extends \atk4\ui\FormField\Line
{
    public $content = 20; // default

    public function init()
    {
        parent::init();

        $this->actionLeft = new \atk4\ui\Button(['icon'=> 'minus']);
        $this->action = new \atk4\ui\Button(['icon'=> 'plus']);

        $this->actionLeft->js('click', $this->jsInput()->val(new \atk4\ui\jsExpression('parseInt([])-1', [$this->jsInput()->val()])));
        $this->action->js('click', $this->jsInput()->val(new \atk4\ui\jsExpression('parseInt([])+1', [$this->jsInput()->val()])));
    }
}
}

// Test 1 - Basic reloading
$layout->add(['Header', 'Virtual Page Logic']);

$vp = $layout->add('VirtualPage'); // this page will not be visible unless you trigger it specifically
$vp->add(['Header', 'Contens of your pop-up here']);
$vp->add(['LoremIpsum', 'size'=>2]);
$vp->add(new Counter());

$bar = $layout->add(['View', 'ui'=>'buttons']);
$bar->add('Button')->set('Inside current layout')->link($vp->getURL());
$bar->add('Button')->set('On a blank page')->link($vp->getURL('popup'));
$bar->add('Button')->set('No layout at all')->link($vp->getURL('cut'));

$layout->add(['Header', 'Actual pop-ups']);

$bar = $layout->add(['View', 'ui'=>'buttons']);
$bar->add('Button')->set('Open in Pop-up')->on('click', new \atk4\ui\jsExpression('window.open([], "", "width=800,height=500")', [$vp->getURL('popup')]));
$bar->add('Button')->set('Load in Modal')->on('click', new \atk4\ui\jsModal('My Popup Title', $vp->getURL('cut')));

$bar->add('Button')->set('Simulate slow load')->on('click', new \atk4\ui\jsModal('My Popup Title', $vp->getURL('cut').'&slow=true'));
if (isset($_GET['slow'])) {
    sleep(1);
}

$layout->add(['Header', 'Modal when you click on table row']);
$t = $layout->add(['Table', 'celled'=>true]);
$t->setModel(new SomeData());

$frame = $layout->add('VirtualPage');
$frame->set(function ($frame) {
    $frame->add(['Header', 'Clicked row with ID = '.$_GET['id']]);
});

$t->onRowClick(new \atk4\ui\jsModal('Row Clicked', $frame, ['id'=>$t->jsRow()->data('id')]));

// Old COMPAT code
/*
$t->on('click', 'tr', new \atk4\ui\jsModal(
    'Row Clicked',
    new \atk4\ui\jsExpression(
        '[]+"&id="+[]', [
            $frame->getURL('cut'),
            (new \atk4\ui\jQuery(new \atk4\ui\jsExpression('this')))->data('id'),
        ]
    )
));
$t->addStyle('cursor', 'pointer');
 */
