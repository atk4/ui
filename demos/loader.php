<?php

require 'init.php';
/*

$a = $app->add(['Button','A']);
$a->js(true, new \atk4\ui\jsExpression('console.log(1)'));
$b = $app->add(['Button','B']);
$b->js(true, new \atk4\ui\jsExpression('console.log(2)'));

$a_rel = new \atk4\ui\jsReload($a);
$b_rel = new \atk4\ui\jsReload($b);

 */
$app->add(new \atk4\ui\tests\ViewTester());

/**** Start on page load ***/
$app->add(['Header', 'Attach loader to default view and start it on page load.']);
$l1 = $app->add('Loader');

$l1->set(function ($p) {
    //set your time expensive function here.
    sleep(2);
    $p->add(['Button', 'One']);
    $p->add(new \atk4\ui\LoremIpsum(['size' => 1]));
    $p->add(new \atk4\ui\tests\ViewTester());
});

$l1 = $app->add('Loader');

$l1->set(function ($p) {
    //set your time expensive function here.
    $p->add(['Button', 'Two']);
    $p->add(new \atk4\ui\LoremIpsum(['size' => 1]));
    $p->add(new \atk4\ui\tests\ViewTester());
    sleep(2);
    $l2 = $p->add('Loader');

    $l2->set(function ($p) {
        //set your time expensive function here.
        sleep(3);
        $p->add(['Button', 'Three']);
        $p->add(new \atk4\ui\LoremIpsum(['size' => 1]));
        $p->add(new \atk4\ui\tests\ViewTester());
    });
});

exit;

$app->add(['Header', 'Attach loader in supplied view and start it using an action.']);

/*** Start from a user action ***/
$l2 = $app->add(new \atk4\ui\Loader(['loader' => new atk4\ui\View(['ui' => 'segment'])]));
$l2->set(function ($loader) {
    //set your time expensive function here.
    sleep(2);
    $loader->add(new \atk4\ui\Message(['text' => 'Load using button']));
});

//Start via user action.
$b = $app->add(['Button', 'Start Loader']);
$b->on('click', $l2->jsStartLoader());

/*** Start from a user action in a button ***/
$app->add(['Header', 'Attach loader to supplied button and start it via same button.']);

$btn_load = new atk4\ui\Button();
$btn_load->set(['Load', 'primary']);

$l3 = $app->add(new \atk4\ui\Loader(['loader' => $btn_load]));
$l3->set(function ($loader) {
    //set your time expensive function here.
    sleep(2);
    $loader->set(['Loaded']);
    $loader->addClass('disabled');
});

//Start via user action.
$btn_load->on('click', $l3->jsStartLoader());
