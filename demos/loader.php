<?php

require_once __DIR__ . '/init.php';

$app->add(['Button', 'Loader Examples - Page 2', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['loader2']);

// ViewTester will perform callback to self.
$app->add(new \atk4\ui\tests\ViewTester());

// Example 1 - Basic usage of a Loader.
$app->add('Loader')->set(function ($p) {
    //set your time expensive function here.
    sleep(2);
    $p->add(['Header', 'Loader #1']);
    $p->add(new \atk4\ui\LoremIpsum(['size' => 1]));

    // Any dynamic views can perform call-backs just fine
    $p->add(new \atk4\ui\tests\ViewTester());

    // Loader may be inside another loader, works fine.
    $loader = $p->add('Loader');

    // use loadEvent to prevent manual loading or even specify custom trigger event
    $loader->loadEvent = false;
    $loader->set(function ($p) {

        // You may pass arguments to the loader, in this case it's "color"
        sleep(3);
        $p->add(['Header', 'Loader #1b - '.$_GET['color']]);
        $p->add(['ui' => $_GET['color'].' segment'])->add(new \atk4\ui\LoremIpsum(['size' => 1]));

        // don't forget to make your own argument sticky so that Components can communicate with themselves:
        $p->app->stickyGet('color');
        $p->add(new \atk4\ui\tests\ViewTester());

        // This loader takes 5s to load because it needs to go through 2 sleep statements.
    });

    // button may contain load event.
    $p->add(['Button', 'Load Segment Manually (5s)', 'red'])->js('click', $loader->jsLoad(['color' => 'red']));
    $p->add(['Button', 'Load Segment Manually (5s)', 'blue'])->js('click', $loader->jsLoad(['color' => 'blue']));
});

// Example 2 - Loader with custom body.
$app->add([
    'Loader',
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
