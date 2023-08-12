<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Data\Model;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpressionable;

class Menu extends View
{
    public $ui = 'menu';

    /**
     * If you set this to false, then upon clicking on the item, it won't
     * be highlighted as "active". This is useful if you have action on your
     * menu and page does not actually reload.
     *
     * @var bool
     */
    public $activateOnClick = true;

    public $defaultTemplate = 'menu.html';

    /**
     * Will be set to true, when Menu is used as a part of a dropdown.
     *
     * @internal
     *
     * @var bool
     */
    public $inDropdown = false;

    /**
     * $seed can also be name here.
     *
     * @param string|array|MenuItem                                                      $item
     * @param string|array<0|string, string|int|false>|JsExpressionable|Model\UserAction $action
     *
     * @return MenuItem
     */
    public function addItem($item = null, $action = null)
    {
        if (!is_object($item)) {
            if (!is_array($item)) {
                $item = [$item];
            }

            array_unshift($item, MenuItem::class);
        }

        /** @var MenuItem */
        $item = $this->add($item);

        if (is_string($action) || is_array($action)) {
            $url = $this->url($action);
            $item->link($url);
        } elseif ($action) {
            $item->on('click', null, $action);
        }

        return $item;
    }

    /**
     * Adds header.
     *
     * @param string $name
     *
     * @return MenuItem
     */
    public function addHeader($name)
    {
        return MenuItem::addTo($this, [$name])->addClass('header');
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
        $subMenu = (self::class)::addTo($this, ['defaultTemplate' => 'submenu.html', 'ui' => 'dropdown', 'inDropdown' => true]);

        if (!is_array($name)) {
            $name = [$name];
        }

        $label = $name['title'] ?? $name['text'] ?? $name['name'] ?? $name[0] ?? null;

        if ($label !== null) {
            $subMenu->template->set('label', $label);
        }

        if (isset($name['icon'])) {
            Icon::addTo($subMenu, [$name['icon']], ['Icon'])->removeClass('item');
        }

        if (!$this->inDropdown) {
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

        if (!is_array($name)) {
            $name = [$name];
        }

        $title = $name['title'] ?? $name['text'] ?? $name['name'] ?? $name[0] ?? null;

        if ($title !== null) {
            $group->template->set('title', $title);
        }

        if (isset($name['icon'])) {
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
        if ($this->elements === [] && !$this->content) {
            $this->destroy();

            return '';
        }

        return parent::getHtml();
    }

    protected function renderView(): void
    {
        if ($this->activateOnClick && $this->ui === 'menu') {
            // Fomantic-UI need some JS magic
            $this->on('click', 'a.item', new JsBlock([
                $this->js()->find('.active')->removeClass('active'),
                (new Jquery())->addClass('active'),
            ]), ['preventDefault' => false, 'stopPropagation' => false]);
        }

        if ($this->content) {
            $this->addClass($this->content);
            $this->content = null;
        }

        parent::renderView();
    }
}
