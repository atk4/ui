<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Place menu.
 */
class Menu extends View
{
    public $ui = 'menu';

    public $activate_on_click = true;

    public $defaultTemplate = 'menu.html';

    public $in_dropdown = false;

    /**
     * $seed can also be name here.
     *
     * @param string|array $item
     * @param string|array $action
     *
     * @return Item
     */
    public function addItem($item = null, $action = null)
    {
        if (is_string($item)) {
            $item = ['Item', $item];
        } elseif (is_array($item)) {
            array_unshift($item, 'Item');
        } elseif (!$item) {
            $item = ['Item'];
        }

        $item = $this->add($item)->setElement('a');

        if (is_array($action)) {
            $action = $this->app->url($action);
        }

        if (is_string($action)) {
            $item->setAttr('href', $action);
        }

        if ($action instanceof jsExpressionable) {
            $item->js('click', $action);
        }

        return $item;
    }

    /**
     * Adds header.
     *
     * @param string $name
     *
     * @return Item
     */
    public function addHeader($name)
    {
        return $this->add(new Item($name))->addClass('header');
    }

    /**
     * Adds sub-menu.
     *
     * @param string|array $name
     *
     * @return Menu
     */
    public function addMenu($name)
    {
        if (is_array($name)) {
            $label = $name[0];
            unset($name[0]);
        } else {
            $label = $name;
            $name = [];
        }

        $sub_menu = $this->add([new self(), 'defaultTemplate' => 'submenu.html', 'ui' => 'dropdown', 'in_dropdown' => true]);
        $sub_menu->set('label', $label);

        if (isset($name['icon'])) {
            $sub_menu->add(new Icon($name['icon']), 'Icon')->removeClass('item');
        }

        if (!$this->in_dropdown) {
            $sub_menu->js(true)->dropdown(['on' => 'hover', 'action' => 'hide']);
        }

        return $sub_menu;
    }

    /**
     * Adds menu group.
     *
     * @param string|array $title
     *
     * @return Menu
     */
    public function addGroup($title)
    {
        $group = $this->add([new self(), 'defaultTemplate' => 'menugroup.html', 'ui' => false]);
        if (is_string($title)) {
            $group->set('title', $title);
        } else {
            if ($title['icon']) {
                $group->add(new Icon($title['icon']), 'Icon')->removeClass('item');
            }
            $group->set('title', $title[0]);
        }

        return $group;
    }

    /**
     * Add right positioned menu.
     *
     * @return Menu
     */
    public function addMenuRight()
    {
        $menu = $this->add([new self(), 'ui' => false], 'RightMenu');
        $menu->removeClass('item')->addClass('right menu');

        return $menu;
    }

    /**
     * Add Item.
     *
     * @param View|string  $object New object to add
     * @param string|array $region (or array for full set of defaults)
     *
     * @return View
     */
    public function add($object, $region = null)
    {
        $item = parent::add($object, $region);
        $item->addClass('item');

        return $item;
    }

    /**
     * Adds divider.
     *
     * @return View
     */
    public function addDivider()
    {
        $item = parent::add(['class' => ['divider']]);

        return $item;
    }

    /*
    function setModel($m) {
        foreach ($m as $m) {
        }
    }
     */

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        if ($this->activate_on_click && $this->ui == 'menu') {
            // Semantic UI need some JS magic
            $this->on('click', 'a.item', $this->js()->find('.active')->removeClass('active'), ['preventDefault' => false, 'stopPropagation' => false]);
            $this->on('click', 'a.item', null, ['preventDefault' => false, 'stopPropagation' => false])->addClass('active');
        }

        if ($this->content) {
            $this->addClass($this->content);
            $this->content = null;
        }

        parent::renderView();
    }
}
