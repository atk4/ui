<?php
require 'init.php';

/********** VIRTUAL ******************/

$layout->add(['Header', 'Virtual Page in modal']);

$modal_vp1 = $layout->add(['Modal', 'title' =>'Lorem Ipsum from a virutal page']);
$modal_vp2 = $layout->add(['Modal', 'title' =>'Message from a virutal page']);

$vp = $layout->add('VirtualPage'); // this page will not be visible unless you trigger it specifically
$vp->add(['Header', 'Contens of your pop-up here']);
$vp->add(['LoremIpsum', 'size'=>2]);
$vp->add('Button')->set('Open next virutal page')->on('click', $modal_vp2->show());


$vp1 = $layout->add('VirtualPage'); // this page will not be visible unless you trigger it specifically
$vp1->add('Message')->text->addParagraph('This text belong to a second virtual page');

$modal_vp1->addVirtualPage($vp);
$modal_vp2->addVirtualPage($vp1);

$bar = $layout->add(['View', 'ui'=>'buttons']);
$b = $bar->add('Button')->set('Open first Virtual page');
$b->on('click', $modal_vp1->show());

/********** ANIMATION ***************/

$menu_items = [
    'scale'=>[],
    'flip'=>['horizontal flip','vertical flip'],
    'fade'=>['fade up', 'fade down', 'fade left', 'fade right'],
    'drop'=> [],
    'fly'=>['fly left', 'fly right', 'fly up', 'fly down'],
    'swing' => ['swing left', 'swing right', 'swing up', 'swing down'],
    'slide' => ['slide left', 'slide right', 'slide up', 'slide down'],
    'browse' => ['browse', 'browse right'],
    'static' => ['jiggle', 'flash', 'shake', 'pulse', 'tada', 'bounce']
];

$layout->add(['Header', 'Modal Animation']);

$modal_transition = $layout->add(['Modal', 'title' =>'Animated modal']);
$modal_transition->add('Message')->set('A lot of animated transition available');
$modal_transition->duration(1000);

$menu_bar = $layout->add(['View', 'ui'=>'buttons']);
$main = $menu_bar->add('Menu');
$tm = $main->addMenu('Select Transition');

foreach ($menu_items as $key=>$items) {
    if (!empty($items)) {
        $sm = $tm->addMenu($key);
        foreach ($items as $item) {
            $smi = $sm->addItem($item);
            $smi->on('click', $modal_transition->js()->modal('setting', 'transition', $smi->js()->text())->modal('show'));
        }
    } else {
        $mi = $tm->addItem($key);
        $mi->on('click', $modal_transition->js()->modal('setting', 'transition', $mi->js()->text())->modal('show'));
    }
}

/************** DENY APPROVE *********/

$layout->add(['Header', 'Modal Options']);

$modal_da = $layout->add(['Modal', 'title'=>'Deny / Approve actions']);
$modal_da->add('Message')->set('This modal is only closable via the green button');
$modal_da->addDenyAction('No', new \atk4\ui\jsExpression('function(){window.alert("C\'ant do that"); return false;}'));
$modal_da->addApproveAction('Yes', new \atk4\ui\jsExpression('function(){window.alert("You\'re good to go!");}'));
$modal_da->notClosable();

$menu_bar = $layout->add(['View', 'ui'=>'buttons']);
$b = $menu_bar->add('Button')->set('Show Deny/Approve');
$b->on('click', $modal_da->show());
