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

    public function addItem($name = null, $action = null)
    {
        $item = $this->add(new Item(['element'=>'a']));
        if (!is_null($name)) {
            $item->set($name);
        }

        if (is_array($action)) {
            $action = $this->app->url($action);
        }

        if (is_string($action)) {
            $item->setAttr('href', $action);
        }

        return $item;
    }

    public function addHeader($name)
    {
        return $this->add(new Item($name))->addClass('header');
    }

    public function addMenu($name)
    {
        if (is_array($name)) {
            $label = $name[0];
            unset($name[0]);
        } else {
            $label = $name;
            $name = [];
        }

        $sub_menu = $this->add(new self(), ['defaultTemplate'=>'submenu.html', 'ui'=>'dropdown']);
        $sub_menu->set('label', $label);

        if (isset($name['icon'])) {
            $sub_menu->add(new Icon($name['icon']), 'Icon')->removeClass('item');
        }

        if ($this->ui == 'menu') {
            $sub_menu->js(true)->dropdown(['on'=>'hover', 'action'=>'hide']);
        }

        return $sub_menu;
    }

    public function addGroup($title)
    {
        $group = $this->add(new self(), ['defaultTemplate'=>'menugroup.html', 'ui'=>false]);
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

    public function add($object, $region = null)
    {
        $item = parent::add($object, $region);
        $item->addClass('item');

        return $item;
    }

    /*
    function setModel($m) {
        foreach ($m as $m) {
        }
    }
     */
    public function renderView()
    {
        if ($this->activate_on_click && $this->ui == 'menu') {
            // Semantic UI need some JS magic
            $this->on('click', 'a.item', $this->js()->find('.active')->removeClass('active'), []);
            $this->on('click', 'a.item', null, [])->addClass('active');
        }

        if ($this->content) {
            $this->addClass($this->content);
            $this->content = null;
        }

        parent::renderView();
    }
}
