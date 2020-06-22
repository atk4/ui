<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/**
 * Demonstrates how to use menu.
 */
/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$m = \atk4\ui\Menu::addTo($app);
$m->addItem('foo', 'foo.php');
$m->addItem('bar');
$m->addItem('baz');
$d = \atk4\ui\DropDown::addTo($m, ['With Callback', 'js' => ['on' => 'hover']]);
$d->setSource(['a', 'b', 'c']);
$d->onChange(function ($item) {
    return 'New seleced item: ' . $item;
});

$sm = $m->addMenu('Sub-menu');
$sm->addItem('one', 'one.php');
$sm->addItem(['two', 'label' => 'VIP', 'disabled']);

$sm = $sm->addMenu('Sub-menu');
$sm->addItem('one');
$sm->addItem('two');

$m = \atk4\ui\Menu::addTo($app, ['vertical pointing']);
$m->addItem(['Inbox', 'label' => ['123', 'teal left pointing']]);
$m->addItem('Spam');
\atk4\ui\Form\Field\Input::addTo($m->addItem(), ['placeholder' => 'Search', 'icon' => 'search'])->addClass('transparent');

$m = \atk4\ui\Menu::addTo($app, ['secondary vertical pointing']);
$m->addItem(['Inbox', 'label' => ['123', 'teal left pointing']]);
$m->addItem('Spam');
\atk4\ui\Form\Field\Input::addTo($m->addItem(), ['placeholder' => 'Search', 'icon' => 'search'])->addClass('transparent');
$m = \atk4\ui\Menu::addTo($app, ['vertical']);
$gr = $m->addGroup('Products');
$gr->addItem('Enterprise');
$gr->addItem('Consumer');

$gr = $m->addGroup('Hosting');
$gr->addItem('Shared');
$gr->addItem('Dedicated');

$m = \atk4\ui\Menu::addTo($app, ['vertical']);
$i = $m->addItem();
\atk4\ui\Header::addTo($i, ['size' => 4])->set('Promotions');
\atk4\ui\View::addTo($i, ['element' => 'P'])->set('Check out our promotions');

// menu without any item should not show
\atk4\ui\Menu::addTo($app);
