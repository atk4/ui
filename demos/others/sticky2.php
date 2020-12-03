<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// This demo shows a local impact of a sticky parameters.

if (isset($_GET['name'])) {
    // IMPORTANT: because this is an optional frame, I have to specify it's unique short_name explicitly, othrewise
    // the name for a second frame will be affected by presence of GET['name'] parameter
    $frame = \Atk4\Ui\View::addTo($app, ['ui' => 'red segment', 'short_name' => 'fr1']);
    $frame->stickyGet('name');

    // frame will generate URL with sticky parameter
    \Atk4\Ui\Label::addTo($frame, ['Name:', 'detail' => $_GET['name'], 'black'])->link($frame->url());

    // app still generates URL without localized sticky
    \Atk4\Ui\Label::addTo($frame, ['Reset', 'iconRight' => 'close', 'black'])->link($app->url());
    \Atk4\Ui\View::addTo($frame, ['ui' => 'hidden divider']);

    // nested interractive elemetns will respect lockal sticky get
    \Atk4\Ui\Button::addTo($frame, ['Triggering callback here will inherit color'])->on('click', function () {
        return new \Atk4\Ui\JsNotify('Color was = ' . $_GET['name']);
    });

    // Next we have loader, which will dynamically load console which will dynamically output "success" message.
    \Atk4\Ui\Loader::addTo($frame)->set(function ($page) {
        \Atk4\Ui\Console::addTo($page)->set(function ($console) {
            $console->output('success!, color is still ' . $_GET['name']);
        });
    });
}

$t = \Atk4\Ui\Table::addTo($app);
$t->setSource(['Red', 'Green', 'Blue']);
$t->addDecorator('name', [\Atk4\Ui\Table\Column\Link::class, [], ['name']]);

$frame = \Atk4\Ui\View::addTo($app, ['ui' => 'green segment']);
\Atk4\Ui\Button::addTo($frame, ['does not inherit sticky get'])->on('click', function () use ($app) {
    return new \Atk4\Ui\JsNotify('$_GET = ' . $app->encodeJson($_GET));
});

\Atk4\Ui\Header::addTo($app, ['Use of View::url()']);

$b1 = \Atk4\Ui\Button::addTo($app);
$b1->set($b1->url());

\Atk4\Ui\Loader::addTo($app)->set(function ($page) use ($b1) {
    $b2 = \Atk4\Ui\Button::addTo($page);
    $b2->set($b2->url());

    $b2->on('click', new \Atk4\Ui\JsReload($b1));
});

$b3 = \Atk4\Ui\Button::addTo($app);
$b3->set($b3->url());
