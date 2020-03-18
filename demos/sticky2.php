<?php

require_once __DIR__ . '/init.php';

// This demo shows a local impact of a sticky parameters.

if (isset($_GET['name'])) {

    // IMPORTANT: because this is an optional frame, I have to specify it's unique short_name explicitly, othrewise
    // the name for a second frame will be affected by presence of GET['name'] parameter
    $frame = $app->add(['View', 'ui'=>'red segment', 'short_name'=>'fr1']);
    $frame->stickyGet('name');

    // frame will generate URL with sticky parameter
    $frame->add(['Label', 'Name:', 'detail'=>$_GET['name'], 'black'])->link($frame->url());

    // app still generates URL without localized sticky
    $frame->add(['Label', 'Reset', 'iconRight'=>'close', 'black'])->link($app->url());
    $frame->add(['View', 'ui'=>'hidden divider']);

    // nested interractive elemetns will respect lockal sticky get
    $frame->add(['Button', 'Triggering callback here will inherit color'])->on('click', function () {
        return new \atk4\ui\jsNotify('Color was = '.$_GET['name']);
    });

    // Next we have loader, which will dynamically load console which will dynamically output "success" message.
    $frame->add('Loader')->set(function ($page) {
        $page->add('Console')->set(function ($console) {
            $console->output('success!, color is still '.$_GET['name']);
        });
    });
}

$t = $app->add(['Table']);
$t->setSource(['Red', 'Green', 'Blue']);
$t->addDecorator('name', ['Link', [], ['name']]);

$frame = $app->add(['View', 'ui'=>'green segment']);
$frame->add(['Button', 'does not inherit sticky get'])->on('click', function () {
    return new \atk4\ui\jsNotify('$_GET = '.json_encode($_GET));
});

$app->add(['Header', 'Use of View::url()']);

$b1 = $app->add('Button');
$b1->set($b1->url());

$app->add('Loader')->set(function ($page) use ($b1) {
    $b2 = $page->add('Button');
    $b2->set($b2->url());

    $b2->on('click', new \atk4\ui\jsReload($b1));
});

$b3 = $app->add('Button');
$b3->set($b3->url());
