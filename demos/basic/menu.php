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
$dropdown = \atk4\ui\Dropdown::addTo($menu, ['With Callback', 'dropdownOptions' => ['on' => 'hover']]);
$dropdown->setSource(['a', 'b', 'c']);
$dropdown->onChange(function ($itemId) {
    return 'New seleced item id: ' . $itemId;
});

$submenu = $menu->addMenu('Sub-menu');
$submenu->addItem('one', 'one.php');
$submenu->addItem(['two', 'label' => 'VIP', 'disabled']);

$submenu = $submenu->addMenu('Sub-menu');
$submenu->addItem('one');
$submenu->addItem('two');

$menu = \atk4\ui\Menu::addTo($app, ['vertical pointing']);
$menu->addItem(['Inbox', 'label' => ['123', 'teal left pointing']]);
$menu->addItem('Spam');
\atk4\ui\Form\Control\Input::addTo($menu->addItem(), ['placeholder' => 'Search', 'icon' => 'search'])->addClass('transparent');

$menu = \atk4\ui\Menu::addTo($app, ['secondary vertical pointing']);
$menu->addItem(['Inbox', 'label' => ['123', 'teal left pointing']]);
$menu->addItem('Spam');
\atk4\ui\Form\Control\Input::addTo($menu->addItem(), ['placeholder' => 'Search', 'icon' => 'search'])->addClass('transparent');
$menu = \atk4\ui\Menu::addTo($app, ['vertical']);
$group = $menu->addGroup('Products');
$group->addItem('Enterprise');
$group->addItem('Consumer');

$group = $menu->addGroup('Hosting');
$group->addItem('Shared');
$group->addItem('Dedicated');

$menu = \atk4\ui\Menu::addTo($app, ['vertical']);
$i = $menu->addItem();
\atk4\ui\Header::addTo($i, ['size' => 4])->set('Promotions');
\atk4\ui\View::addTo($i, ['element' => 'P'])->set('Check out our promotions');

// menu without any item should not show
\atk4\ui\Menu::addTo($app);
