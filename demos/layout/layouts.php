<?php

chdir('..');
require_once dirname(__DIR__) . '/atk-init.php';

// buttons configuration: [page, title]
$buttons = [
    ['page' => ['layouts_nolayout'],               'title' => 'HTML without layout'],
    ['page' => ['layouts_manual'],                 'title' => 'Manual layout'],
    ['page' => ['/demos/basic/header', 'layout' => 'Centered'], 'title' => 'Centered layout'],
    ['page' => ['layouts_admin'],                  'title' => 'Admin Layout'],
    ['page' => ['layouts_error'],                  'title' => 'Exception Error'],
];

// layout
\atk4\ui\Text::addTo(\atk4\ui\View::addTo($app, ['red' => true,  'ui' => 'segment']))
    ->addParagraph('Layouts can be used to wrap your UI elements into HTML / Boilerplate');

// toolbar
$tb = \atk4\ui\View::addTo($app);

// iframe
$i = \atk4\ui\View::addTo($app, ['green' => true, 'ui' => 'segment'])->setElement('iframe')->setStyle(['width' => '100%', 'height' => '500px']);

// add buttons in toolbar
foreach ($buttons as $k => $args) {
    \atk4\ui\Button::addTo($tb)
        ->set([$args['title'], 'iconRight' => 'down arrow'])
        ->js('click', $i->js()->attr('src', $app->url($args['page'])));
}
