<?php

declare(strict_types=1);

namespace Atk4\Ui;

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
     * @var bool
     */
    public $in_dropdown = false;

    /**
     * $seed can also be name here.
     *
     * @param string|array|Item $item
     * @param string|array      $action
     *
     * @return Item
     */
    public function addItem($item = null, $action = null)
    {
        if (!is_object($item)) {
            $item = (array) $item;

            array_unshift($item, Item::class);
        }

        $item = $this->add($item)->setElement('a');

        if (is_string($action) || is_array($action)) {
            $action = $this->url($action);
        }

        if (is_string($action)) {
            $item->setAttr('href', $action);
        }

        if ($action instanceof JsExpressionable) {
            $item->js('click', $action);
        }

        if ($action instanceof UserAction\ExecutorInterface) {
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
        return Item::addTo($this, [$name])->addClass('header');
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
        $subMenu = (self::class)::addTo($this, ['defaultTemplate' => 'submenu.html', 'ui' => 'dropdown', 'in_dropdown' => true]);

        $name = (array) $name;

        $label = $name['title'] ?? $name['text'] ?? $name['name'] ?? $name[0] ?? null;

        if (isset($label)) {
            $subMenu->set('label', $label);
        }

        if (!empty($name['icon'])) {
            Icon::addTo($subMenu, [$name['icon']], ['Icon'])->removeClass('item');
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
    public function addGroup($name, string $template = 'menugroup.html')
    {
        $group = (self::class)::addTo($this, ['defaultTemplate' => $template, 'ui' => false]);

        $name = (array) $name;

        $title = $name['title'] ?? $name['text'] ?? $name['name'] ?? $name[0] ?? null;

        if (isset($title)) {
            $group->set('title', $title);
        }

        if (!empty($name['icon'])) {
            Icon::addTo($group, [$name['icon']], ['Icon'])->removeClass('item');
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
        return (self::class)::addTo($this, ['ui' => false], ['RightMenu'])->removeClass('item')->addClass('right menu');
    }

    /**
     * Add Item.
     *
     * @param View|string|array $seed   New object to add
     * @param string|array|null $region
     */
    public function add($seed, $region = null): AbstractView
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
        return parent::add([View::class, 'class' => ['divider']]);
    }

    public function getHtml()
    {
        // if menu don't have a single element or content, then destroy it
        if (empty($this->elements) && !$this->content) {
            $this->destroy();

            return '';
        }

        return parent::getHtml();
    }

    protected function renderView(): void
    {
        if ($this->activate_on_click && $this->ui === 'menu') {
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
