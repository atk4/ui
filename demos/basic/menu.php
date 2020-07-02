<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/**
 * Demonstrates how to use menu.
 */
/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$menu = \atk4\ui\Menu::addTo($app);
$menu->addItem('foo', 'foo.php');
$menu->addItem('bar');
$menu->addItem('baz');
$d = \atk4\ui\DropDown::addTo($menu, ['With Callback', 'js' => ['on' => 'hover']]);
$d->setSource(['a', 'b', 'c']);
$d->onChange(function ($item) {
    return 'New seleced item: ' . $item;
});

$sm = $menu->addMenu('Sub-menu');
$sm->addItem('one', 'one.php');
$sm->addItem(['two', 'label' => 'VIP', 'disabled']);

$sm = $sm->addMenu('Sub-menu');
$sm->addItem('one');
$sm->addItem('two');

$menu = \atk4\ui\Menu::addTo($app, ['vertical pointing']);
$menu->addItem(['Inbox', 'label' => ['123', 'teal left pointing']]);
$menu->addItem('Spam');
\atk4\ui\Form\Control\Input::addTo($menu->addItem(), ['placeholder' => 'Search', 'icon' => 'search'])->addClass('transparent');

$menu = \atk4\ui\Menu::addTo($app, ['secondary vertical pointing']);
$menu->addItem(['Inbox', 'label' => ['123', 'teal left pointing']]);
$menu->addItem('Spam');
\atk4\ui\Form\Control\Input::addTo($menu->addItem(), ['placeholder' => 'Search', 'icon' => 'search'])->addClass('transparent');
$menu = \atk4\ui\Menu::addTo($app, ['vertical']);
$gr = $menu->addGroup('Products');
$gr->addItem('Enterprise');
$gr->addItem('Consumer');

$gr = $menu->addGroup('Hosting');
$gr->addItem('Shared');
$gr->addItem('Dedicated');

$menu = \atk4\ui\Menu::addTo($app, ['vertical']);
$i = $menu->addItem();
\atk4\ui\Header::addTo($i, ['size' => 4])->set('Promotions');
\atk4\ui\View::addTo($i, ['element' => 'P'])->set('Check out our promotions');

// menu without any item should not show
\atk4\ui\Menu::addTo($app);
