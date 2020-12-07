<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/**
 * Demonstrates how to use menu.
 */
/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$menu = \Atk4\Ui\Menu::addTo($app);
$menu->addItem('foo', 'foo.php');
$menu->addItem('bar');
$menu->addItem('baz');
$dropdown = \Atk4\Ui\Dropdown::addTo($menu, ['With Callback', 'dropdownOptions' => ['on' => 'hover']]);
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

$menu = \Atk4\Ui\Menu::addTo($app, ['vertical pointing']);
$menu->addItem(['Inbox', 'label' => ['123', 'teal left pointing']]);
$menu->addItem('Spam');
\Atk4\Ui\Form\Control\Input::addTo($menu->addItem(), ['placeholder' => 'Search', 'icon' => 'search'])->addClass('transparent');

$menu = \Atk4\Ui\Menu::addTo($app, ['secondary vertical pointing']);
$menu->addItem(['Inbox', 'label' => ['123', 'teal left pointing']]);
$menu->addItem('Spam');
\Atk4\Ui\Form\Control\Input::addTo($menu->addItem(), ['placeholder' => 'Search', 'icon' => 'search'])->addClass('transparent');
$menu = \Atk4\Ui\Menu::addTo($app, ['vertical']);
$group = $menu->addGroup('Products');
$group->addItem('Enterprise');
$group->addItem('Consumer');

$group = $menu->addGroup('Hosting');
$group->addItem('Shared');
$group->addItem('Dedicated');

$menu = \Atk4\Ui\Menu::addTo($app, ['vertical']);
$i = $menu->addItem();
\Atk4\Ui\Header::addTo($i, ['size' => 4])->set('Promotions');
\Atk4\Ui\View::addTo($i, ['element' => 'P'])->set('Check out our promotions');

// menu without any item should not show
\Atk4\Ui\Menu::addTo($app);
