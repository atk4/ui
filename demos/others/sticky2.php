<?php

require_once __DIR__ . '/../atk-init.php';

// This demo shows a local impact of a sticky parameters.

if (isset($_GET['name'])) {

    // IMPORTANT: because this is an optional frame, I have to specify it's unique short_name explicitly, othrewise
    // the name for a second frame will be affected by presence of GET['name'] parameter
    $frame = \atk4\ui\View::addTo($app, ['ui'=>'red segment', 'short_name'=>'fr1']);
    $frame->stickyGet('name');

    // frame will generate URL with sticky parameter
    \atk4\ui\Label::addTo($frame, ['Name:', 'detail'=>$_GET['name'], 'black'])->link($frame->url());

    // app still generates URL without localized sticky
    \atk4\ui\Label::addTo($frame, ['Reset', 'iconRight'=>'close', 'black'])->link($app->url());
    \atk4\ui\View::addTo($frame, ['ui'=>'hidden divider']);

    // nested interractive elemetns will respect lockal sticky get
    \atk4\ui\Button::addTo($frame, ['Triggering callback here will inherit color'])->on('click', function () {
        return new \atk4\ui\jsNotify('Color was = ' . $_GET['name']);
    });

    // Next we have loader, which will dynamically load console which will dynamically output "success" message.
    \atk4\ui\Loader::addTo($frame)->set(function ($page) {
        \atk4\ui\Console::addTo($page)->set(function ($console) {
            $console->output('success!, color is still ' . $_GET['name']);
        });
    });
}

$t = \atk4\ui\Table::addTo($app);
$t->setSource(['Red', 'Green', 'Blue']);
$t->addDecorator('name', ['Link', [], ['name']]);

$frame = \atk4\ui\View::addTo($app, ['ui'=>'green segment']);
\atk4\ui\Button::addTo($frame, ['does not inherit sticky get'])->on('click', function () {
    return new \atk4\ui\jsNotify('$_GET = ' . json_encode($_GET));
});

\atk4\ui\Header::addTo($app, ['Use of View::url()']);

$b1 = \atk4\ui\Button::addTo($app);
$b1->set($b1->url());

\atk4\ui\Loader::addTo($app)->set(function ($page) use ($b1) {
    $b2 = \atk4\ui\Button::addTo($page);
    $b2->set($b2->url());

    $b2->on('click', new \atk4\ui\jsReload($b1));
});

$b3 = \atk4\ui\Button::addTo($app);
$b3->set($b3->url());
