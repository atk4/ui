<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'Actions', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['actions']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'Extensions to ATK Data Actions', 'subHeader'=>'Model action can be trigger using js Event']);

$country = new Country($db);

$c_action = $country->addAction('Email', function ($m) {
    return 'Email to Kristy in '.$m->get('name').' has been sent!';
});

$country->tryLoadAny();

$card = $app->add('Card');
$content = new \atk4\ui\View(['class' => ['content']]);
$content->add($img = new \atk4\ui\Image(['images/kristy.png']));
$img->addClass('right floated mini ui image');
$content->add($header = new \atk4\ui\Header(['Kristy']));

$card->addContent($content);
$card->addDescription('Kristy is a friend of Mully.');

$s = $card->addSection('Country');
$s->addFields($country->loadAny(), ['name', 'iso']);

$card->addClickAction($c_action);

///////////////////////////////////////////

$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'Action can ask for confirmation before executing', 'size' => 4]);

$files = new File($app->db);
$f_action = $files->addAction(
    'import_from_filesystem',
    [
        'callback'=> 'importFromFilesystem',
        'args'    => [
            'path'=> '.',
        ],
        'scope'=> atk4\data\UserAction\Generic::NO_RECORDS,
    ]
);

$btn = $app->add(['Button', 'Import File']);
$executor = $app->add(new \atk4\ui\ActionExecutor\jsUserAction());
$executor->setAction($f_action, [8, 'path' => '.']);
$executor->addHook('afterExecute', function ($t, $m) {
    return new \atk4\ui\jsToast('Files imported');
});

$btn->on('click', $executor, ['confirm'=> 'This will import a lot of file. Are you sure?']);
