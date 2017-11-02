<?php

require 'init.php';


//$vp = $app->add('VirtualPage');
//
//$vp->set(function($p){
//    $p->add(['Message', 'Message'])->text->addParagraph('youhoy');
//});

$sse = $app->add('SSE'/*new atk4\ui\SSE()*/);
//$sse->set(function ($p){
//   $p->add('View')->set('allo');
//});

$msg = $app->add(['Message', 'Message'])->text->addParagraph('Time is: ' . time());

$sse->addViewEventHandler($msg, function($msg) {
   $msg->text->addParagraph('Time is: ');
});
$t = 't';

// SSE is a virtual page like and does not output anything
// SSE::update('eventType', 'event', eventAction = null);
//      when updating need a way to check if event needs update
//          probably via timestamps or header check.
// SSE defaults should be settable, like time to reconnect etc.
// SSE eventType: jsAction, htmlRender
// SSE event: the view to act on
// SSE eventAction: append, replace etc...
//      Perhaps should output view-id.