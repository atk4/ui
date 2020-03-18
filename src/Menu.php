<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

use atk4\data\UserAction\Generic;

/**
 * Place menu.
 */
class Menu extends View
{
    public $ui = 'menu';

    /**
     * if you set this to false, then upon clicking on the item, it won't
     * be highlighted as "active". This is useful if you have action on your
     * menu and page does not actually reload.
     *
     * @var bool
     */
    public $activate_on_click = true;

    public $defaultTemplate = 'menu.html';

    /**
     * will be set to true, when Menu is used as a part of a dropdown.
     *
     * @internal
     *
     * @var [type]
     */
    public $in_dropdown = false;

    /**
     * $seed can also be name here.
     *
     * @param string|array|Item $item
     * @param string|array $action
     *
     * @return Item
     */
    public function addItem($item = null, $action = null)
    {
        if (!is_object($item)) {
            $item = (array) $item;

            array_unshift($item, 'Item');
        }

        $item = $this->add($item)->setElement('a');

        if (is_string($action) || is_array($action)) {
            $action = $this->url($action);
        }

        if (is_string($action)) {
            $item->setAttr('href', $action);
        }

        if ($action instanceof jsExpressionable) {
            $item->js('click', $action);
        }

        if ($action instanceof Generic) {
            $item->on('click', $action);
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
        $subMenu = $this->add([new self(), 'defaultTemplate' => 'submenu.html', 'ui' => 'dropdown', 'in_dropdown' => true]);

        $name = (array) $name;

        $label = $name['title'] ?? $name['text'] ?? $name['name'] ?? $name[0] ?? null;

        if (isset($label)) {
            $subMenu->set('label', $label);
        }

        if (!empty($name['icon'])) {
            $subMenu->add(new Icon($name['icon']), 'Icon')->removeClass('item');
        }

        if (!$this->in_dropdown) {
            $subMenu->js(true)->dropdown(['on' => 'hover', 'action' => 'hide']);
        }

        return $subMenu;
    }

    /**
     * Adds menu group.
     *
     * @param string|array $name
     *
     * @return Menu
     */
    public function addGroup($name)
    {
        $group = $this->add([new self(), 'defaultTemplate' => 'menugroup.html', 'ui' => false]);

        $name = (array) $name;

        $title = $name['title'] ?? $name['text'] ?? $name['name'] ?? $name[0] ?? null;

        if (isset($title)) {
            $group->set('title', $title);
        }

        if (!empty($name['icon'])) {
            $group->add(new Icon($name['icon']), 'Icon')->removeClass('item');
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
        return $this->add([new self(), 'ui' => false], 'RightMenu')->removeClass('item')->addClass('right menu');
    }

    /**
     * Add Item.
     *
     * @param View|string|array $seed   New object to add
     * @param string|array|null $region
     *
     * @return View
     */
    public function add($seed, $region = null)
    {
        return parent::add($seed, $region)->addClass('item');
    }

    /**
     * Adds divider.
     *
     * @return View
     */
    public function addDivider()
    {
        return parent::add(['View', 'class' => ['divider']]);
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
    public function getHTML()
    {
        // if menu don't have a single element or content, then destroy it
        if (empty($this->elements) && !$this->content) {
            $this->destroy();

            return '';
        }

        return parent::getHTML();
    }

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
