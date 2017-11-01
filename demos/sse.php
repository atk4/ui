<?php

require 'init.php';

$v = $app->add('View')->set('allo');
$sse = $app->add('SSE'/*new atk4\ui\SSE()*/);

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