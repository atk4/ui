<?php

require_once __DIR__ . '/init.php';

\atk4\ui\Button::addTo($app, ['Loader Examples - Page 2', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['loader2']);

// ViewTester will perform callback to self.
\atk4\ui\tests\ViewTester::addTo($app);

// Example 1 - Basic usage of a Loader.
\atk4\ui\Loader::addTo($app)->set(function ($p) {
    //set your time expensive function here.
    sleep(2);
    \atk4\ui\Header::addTo($p, ['Loader #1']);
    $p->add(new \atk4\ui\LoremIpsum(['size' => 1]));

    // Any dynamic views can perform call-backs just fine
    \atk4\ui\tests\ViewTester::addTo($p);

    // Loader may be inside another loader, works fine.
    $loader = \atk4\ui\Loader::addTo($p);

    // use loadEvent to prevent manual loading or even specify custom trigger event
    $loader->loadEvent = false;
    $loader->set(function ($p) {

        // You may pass arguments to the loader, in this case it's "color"
        sleep(3);
        \atk4\ui\Header::addTo($p, ['Loader #1b - '.$_GET['color']]);
        \atk4\ui\View::addTo($p, ['ui' => $_GET['color'].' segment'])->add(new \atk4\ui\LoremIpsum(['size' => 1]));

        // don't forget to make your own argument sticky so that Components can communicate with themselves:
        $p->app->stickyGet('color');
        \atk4\ui\tests\ViewTester::addTo($p);

        // This loader takes 5s to load because it needs to go through 2 sleep statements.
    });

    // button may contain load event.
    \atk4\ui\Button::addTo($p, ['Load Segment Manually (5s)', 'red'])->js('click', $loader->jsLoad(['color' => 'red']));
    \atk4\ui\Button::addTo($p, ['Load Segment Manually (5s)', 'blue'])->js('click', $loader->jsLoad(['color' => 'blue']));
});

// Example 2 - Loader with custom body.
\atk4\ui\Loader::addTo($app, [
    'ui'   => '',   // this will prevent "loading spinner" from showing
    'shim' => [   // shim is displayed while content is leaded
        'Message',
        'Generating LoremIpsum, please wait...',
        'red',
    ],
])->set(function ($p) {
    sleep(1);
    $p->add(new \atk4\ui\LoremIpsum(['size' => 2]));
});
