<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Loader;
use Atk4\Ui\LoremIpsum;
use Atk4\Ui\Message;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Loader Examples - Page 2', 'class.small right floated basic blue' => true, 'iconRight' => 'right arrow'])
    ->link(['loader2']);

View::addTo($app, ['ui' => 'clearing divider']);

// ViewTester will perform callback to self.
ViewTester::addTo($app);

// Example 1 - Basic usage of a Loader.
Loader::addTo($app)->set(function (Loader $p) {
    // set your time expensive function here.
    sleep(1);
    Header::addTo($p, ['Loader #1']);
    LoremIpsum::addTo($p, ['size' => 1]);

    // Any dynamic views can perform call-backs just fine
    ViewTester::addTo($p);

    // Loader may be inside another loader, works fine.
    $loader = Loader::addTo($p);

    // use loadEvent to prevent manual loading or even specify custom trigger event
    $loader->loadEvent = false;
    $loader->set(function (Loader $p) {
        // You may pass arguments to the loader, in this case it's "color"
        sleep(1);
        Header::addTo($p, ['Loader #1b - ' . $_GET['color']]);
        LoremIpsum::addTo(View::addTo($p, ['ui' => $_GET['color'] . ' segment']), ['size' => 1]);

        // don't forget to make your own argument sticky so that Components can communicate with themselves:
        $p->stickyGet('color');
        ViewTester::addTo($p);

        // This loader takes 2s to load because it needs to go through 2 sleep statements.
    });

    // button may contain load event.
    Button::addTo($p, ['Load Segment Manually (2s)', 'class.red' => true])->js('click', $loader->jsLoad(['color' => 'red']));
    Button::addTo($p, ['Load Segment Manually (2s)', 'class.blue' => true])->js('click', $loader->jsLoad(['color' => 'blue']));
});

// Example 2 - Loader with custom body.
Loader::addTo($app, [
    'ui' => '', // this will prevent "loading spinner" from showing
    'shim' => [ // shim is displayed while content is leaded
        Message::class,
        'Generating LoremIpsum, please wait...',
        'class.red' => true,
    ],
])->set(function (Loader $p) {
    sleep(1);
    LoremIpsum::addTo($p, ['size' => 2]);
});
