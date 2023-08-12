<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Layout;
use Atk4\Ui\Text;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// buttons configuration: [page, title]
$buttons = [
    ['page' => ['layouts_nolayout'], 'title' => 'HTML without layout'],
    ['page' => ['layouts_manual'], 'title' => 'Manual layout'],
    ['page' => ['../basic/header', 'layout' => Layout\Centered::class], 'title' => 'Centered layout'],
    ['page' => ['layouts_admin'], 'title' => 'Admin Layout'],
    ['page' => ['layouts_error'], 'title' => 'Exception Error'],
];

// layout
Text::addTo(View::addTo($app, ['class.red' => true, 'ui' => 'segment']))
    ->addParagraph('Layouts can be used to wrap your UI elements into HTML / Boilerplate');

// toolbar
$tb = View::addTo($app);

// iframe
$i = View::addTo($app, ['class.green' => true, 'ui' => 'segment'])->setElement('iframe')->setStyle(['width' => '100%', 'height' => '500px']);

// add buttons in toolbar
foreach ($buttons as $k => $args) {
    $button = Button::addTo($tb, [$args['title'], 'iconRight' => 'down arrow']);
    $button->on('click', $i->js()->attr('src', $app->url($args['page'])));
}
