<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Dropdown as UiDropdown;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Menu;
use Atk4\Ui\ViewWithContent;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$menu = Menu::addTo($app);
$menu->addItem('foo', 'foo.php');
$menu->addItem('bar');
$menu->addItem('baz');
$dropdown = UiDropdown::addTo($menu, ['With Callback', 'dropdownOptions' => ['on' => 'hover']]);
$dropdown->setSource(['a', 'b', 'c']);
$dropdown->onChange(static function (int $id) use ($dropdown) {
    $entity = $dropdown->model->load($id);

    return new JsToast('New selected item: ' . $dropdown->getApp()->uiPersistence->typecastSaveField($dropdown->model->getField('name'), $entity->get('name')));
});

$model = new Category($app->db);
$dropdown2 = UiDropdown::addTo($menu, ['From Model', 'dropdownOptions' => ['on' => 'hover']]);
$dropdown2->setModel($model);
$dropdown2->onChange(static function ($id) use ($app, $model) {
    $entity = $model->load($id);

    return new JsToast('New selected item: ' . $app->uiPersistence->typecastSaveField($model->getField($model->fieldName()->name), $entity->name));
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
ViewWithContent::addTo($i, ['element' => 'p'])->set('Check out our promotions');

// menu without any item should not show
Menu::addTo($app);
