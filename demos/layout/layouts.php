<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// buttons configuration: [page, title]
$buttons = [
    ['page' => ['layouts_nolayout'], 'title' => 'HTML without layout'],
    ['page' => ['layouts_manual'], 'title' => 'Manual layout'],
    ['page' => ['../basic/header', 'layout' => \Atk4\Ui\Layout\Centered::class], 'title' => 'Centered layout'],
    ['page' => ['layouts_admin'], 'title' => 'Admin Layout'],
    ['page' => ['layouts_error'], 'title' => 'Exception Error'],
];

// layout
\Atk4\Ui\Text::addTo(\Atk4\Ui\View::addTo($app, ['red' => true,  'ui' => 'segment']))
    ->addParagraph('Layouts can be used to wrap your UI elements into HTML / Boilerplate');

// toolbar
$tb = \Atk4\Ui\View::addTo($app);

// iframe
$i = \Atk4\Ui\View::addTo($app, ['green' => true, 'ui' => 'segment'])->setElement('iframe')->setStyle(['width' => '100%', 'height' => '500px']);

// add buttons in toolbar
foreach ($buttons as $k => $args) {
    \Atk4\Ui\Button::addTo($tb)
        ->set([$args['title'], 'iconRight' => 'down arrow'])
        ->js('click', $i->js()->attr('src', $app->url($args['page'])));
}
