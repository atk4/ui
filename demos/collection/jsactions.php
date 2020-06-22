<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\UserAction;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Button::addTo($app, ['Actions from jsEvent', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['jsactions2']);

\atk4\ui\Button::addTo($app, ['Actions', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['actions']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Extensions to ATK Data Actions', 'subHeader' => 'Model action can be trigger using js Event']);

$country = new Country($app->db);

$c_action = $country->addAction('Email', function ($m) {
    return 'Email to Kristy in ' . $m->get('name') . ' has been sent!';
});

$country->tryLoadAny();
$card = \atk4\ui\Card::addTo($app);
$content = new \atk4\ui\View(['class' => ['content']]);
$content->add($img = new \atk4\ui\Image(['../images/kristy.png']));
$img->addClass('right floated mini ui image');
$content->add($header = new \atk4\ui\Header(['Kristy']));

$card->addContent($content);
$card->addDescription('Kristy is a friend of Mully.');

$s = $card->addSection('Country');
$s->addFields($country->loadAny(), ['name', 'iso']);

$card->addClickAction($c_action);

///////////////////////////////////////////

\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Action can ask for confirmation before executing', 'size' => 4]);

$files = new File($app->db);
$f_action = $files->addAction(
    'import_from_filesystem',
    [
        'callback' => 'importFromFilesystem',
        'args' => [
            'path' => '.',
        ],
        'scope' => \atk4\data\Model\UserAction::SCOPE_NONE,
    ]
);

$btn = \atk4\ui\Button::addTo($app, ['Import File']);
$executor = UserAction\JsCallbackExecutor::addTo($app);
$executor->setAction($f_action, ['path' => '.']);
$executor->onHook(UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function ($t, $m) {
    return new \atk4\ui\jsToast('Files imported');
});

$btn->on('click', $executor, ['confirm' => 'This will import a lot of file. Are you sure?']);

\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Action can be applied to an input button.', 'size' => 4]);

// Note here that we explicitly required a jsUserAction executor because we want to use the input value
// as the action args.
$country->addAction('greet', [
    'args' => [
        'name' => [
            'type' => 'string',
            'required' => true,
        ],
    ],
    'ui' => ['executor' => [UserAction\JsCallbackExecutor::class]],
    'callback' => function ($m, $name) {
        return 'Hello ' . $name;
    },
]);

\atk4\ui\Form\Control\Line::addTo($app, ['action' => $country->getUserAction('greet')]);
