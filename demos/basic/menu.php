<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Dropdown as UiDropdown;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Menu;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$menu = Menu::addTo($app);
$menu->addItem('foo', 'foo.php');
$menu->addItem('bar');
$menu->addItem('baz');
$dropdown = UiDropdown::addTo($menu, ['With Callback', 'dropdownOptions' => ['on' => 'hover']]);
$dropdown->setSource(['a', 'b', 'c']);
$dropdown->onChange(static function (string $itemId) {
    return 'New selected item ID: ' . $itemId;
});

$submenu = $menu->addMenu('Sub-menu');
$submenu->addItem('one', 'one.php');
$submenu->addItem(['two', 'label' => 'VIP', 'class.disabled' => true]);

$submenu = $submenu->addMenu('Sub-menu');
$submenu->addItem('one');
$submenu->addItem('two');

$menu = Menu::addTo($app, ['vertical pointing']);
$menu->addItem(['Inbox', 'label' => ['123', 'class.teal left pointing' => true]]);
$menu->addItem('Spam');
Form\Control\Line::addTo($menu->addItem(), ['placeholder' => 'Search', 'icon' => 'search'])->addClass('transparent');

$menu = Menu::addTo($app, ['secondary vertical pointing']);
$menu->addItem(['Inbox', 'label' => ['123', 'class.teal left pointing' => true]]);
$menu->addItem('Spam');
Form\Control\Line::addTo($menu->addItem(), ['placeholder' => 'Search', 'icon' => 'search'])->addClass('transparent');
$menu = Menu::addTo($app, ['vertical']);
$group = $menu->addGroup('Products');
$group->addItem('Enterprise');
$group->addItem('Consumer');

$group = $menu->addGroup('Hosting');
$group->addItem('Shared');
$group->addItem('Dedicated');

$menu = Menu::addTo($app, ['vertical']);
$i = $menu->addItem();
Header::addTo($i, ['size' => 4])->set('Promotions');
View::addTo($i, ['element' => 'P'])->set('Check out our promotions');

// menu without any item should not show
Menu::addTo($app);
