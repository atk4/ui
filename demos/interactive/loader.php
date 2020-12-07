<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Loader Examples - Page 2', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['loader2']);

\Atk4\Ui\View::addTo($app, ['ui' => 'clearing divider']);

// ViewTester will perform callback to self.
ViewTester::addTo($app);

// Example 1 - Basic usage of a Loader.
\Atk4\Ui\Loader::addTo($app)->set(function ($p) {
    // set your time expensive function here.
    sleep(1);
    \Atk4\Ui\Header::addTo($p, ['Loader #1']);
    \Atk4\Ui\LoremIpsum::addTo($p, ['size' => 1]);

    // Any dynamic views can perform call-backs just fine
    ViewTester::addTo($p);

    // Loader may be inside another loader, works fine.
    $loader = \Atk4\Ui\Loader::addTo($p);

    // use loadEvent to prevent manual loading or even specify custom trigger event
    $loader->loadEvent = false;
    $loader->set(function ($p) {
        // You may pass arguments to the loader, in this case it's "color"
        sleep(1);
        \Atk4\Ui\Header::addTo($p, ['Loader #1b - ' . $_GET['color']]);
        \Atk4\Ui\LoremIpsum::addTo(\Atk4\Ui\View::addTo($p, ['ui' => $_GET['color'] . ' segment']), ['size' => 1]);

        // don't forget to make your own argument sticky so that Components can communicate with themselves:
        $p->getApp()->stickyGet('color');
        ViewTester::addTo($p);

        // This loader takes 2s to load because it needs to go through 2 sleep statements.
    });

    // button may contain load event.
    \Atk4\Ui\Button::addTo($p, ['Load Segment Manually (2s)', 'red'])->js('click', $loader->jsLoad(['color' => 'red']));
    \Atk4\Ui\Button::addTo($p, ['Load Segment Manually (2s)', 'blue'])->js('click', $loader->jsLoad(['color' => 'blue']));
});

// Example 2 - Loader with custom body.
\Atk4\Ui\Loader::addTo($app, [
    'ui' => '',   // this will prevent "loading spinner" from showing
    'shim' => [   // shim is displayed while content is leaded
        \Atk4\Ui\Message::class,
        'Generating LoremIpsum, please wait...',
        'red',
    ],
])->set(function ($p) {
    usleep(500 * 1000);
    \Atk4\Ui\LoremIpsum::addTo($p, ['size' => 2]);
});
