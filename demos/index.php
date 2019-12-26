<?php

include 'init.php';

$tut_array = [
    ['id'=>'intro', 'name'=>'Agile UI Intro', 'description'=>'Take a quick stroll through some of the amazing features of Agile Toolkit.'],
    ['id'=> 'actions', 'name'=>'New in 2.0: Actions', 'description'=>'Version 2.0 introduced support for universal user actions. Easy to add and supported by all Views.'],
];
$db = new \atk4\data\Persistence_Static($tut_array);
$db->app = $app;
$tutorials = new \atk4\data\Model($db, ['read_only'=>true]);
$tutorials->addAction('Watch', function ($m) {
    return $m->app->jsRedirect(['tutorial-'.$m->id, 'layout'=>'Centered']);
});

$app->add([
    'Header',
    'Welcome to Agile Toolkit!',
    'size'     => 1,
    'subHeader'=> 'Below are some tutorials to walk you through core concepts',
]);
$deck = $app->add(['CardDeck']);
$deck->setModel($tutorials, ['description']);

/*
if (!$app->stickyget('begin')) {
    $app->add('Header')->set('Welcome to Agile Toolkit Demo!!');

    $t = $app->add(['View', false, 'green', 'ui' => 'segment'])->add('Text');
    $t->addParagraph('Take a quick stroll through some of the amazing features of Agile Toolkit.');

    $app->add(['Button', 'Begin the demo..', 'huge primary fluid', 'iconRight' => 'right arrow'])
        ->link(['layout' => 'Centered', 'begin' => true]);

    $app->add('Header')->set('What is new in Agile Toolkit 2.0');

    $t = $app->add(['View', false, 'green', 'ui' => 'segment'])->add('Text');
    $t->addParagraph('In this version of Agile Toolkit we introduce "User Actions"!');

    $app->add(['Button', 'Learn about User Actions', 'huge basic primary fluid', 'iconRight' => 'right arrow'])
        ->link(['tutorial_actions', 'layout' => 'Centered', 'begin' => true]);

    $app->callExit();
}

*/
