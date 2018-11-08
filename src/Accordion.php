<?php

namespace atk4\ui;

/**
 * Accordion is a View holding accordion items.
 *
 * You can add static content to an accordion item or pass a callback
 * for adding content dynamically.
 */
class Accordion extends View
{
    public $defaultTemplate = 'accordion.html';

    public $ui = 'accordion';

    /**
     * The css class for Fomantic-ui accordion type.
     *
     * @var arra|string|ynull
     */
    public $type = null;

    /**
     * Settings as per Fomantic-ui accordion settings.
     *
     * @var array
     */
    public $settings = [];

    /**
     * A collection of AccordionItem in this Accordion;
     *
     * @var array
     */
    public $items = [];

    /**
     * The AccordionItem index number to activate on load.
     *
     * @var int
     */
    public $activeItem = -1;

    /**
     * Add an accordion item.
     * You can add static View within your item or pass
     * a callback for dynamic content.
     *
     * @param string         $title
     * @param null|Callback  $callback
     * @param string         $icon
     *
     * @throws Exception
     *
     * @return View
     */
    public function addItem($title, $callback = null, $icon = 'dropdown')
    {
        $item = $this->add(['AccordionItem', 'title' => $title, 'icon' => $icon]);

        if ($callback) {
            $item->virtualPage = $item->add(['VirtualPage', 'ui' => '']);
            $item->virtualPage->set($callback);
        }

        $this->items[] = $item;

        return $item;
    }

    /**
     * Activate or open an accordion item.
     *
     * @param AccordionItem $item The item to activate.
     */
    public function activate($item)
    {
        $this->activeItem = $this->getItemIdx($item);
    }

    /*
     * JS Behavior wrapper functions.
     */
    public function jsRefresh($when = null)
    {
        return $this->jsBehavior('refresh', [], $when);
    }

    public function jsOpen($item, $when = null)
    {
        return $this->jsBehavior('open', [$this->getItemIdx($item)], $when);
    }

    public function jsCloseOthers($when = null)
    {
        return $this->jsBehavior('close others', [], $when);
    }

    public function jsClose($item, $when = null)
    {
        return $this->jsBehavior('close', [$this->getItemIdx($item)], $when);
    }

    public function jsToggle($item, $when = null)
    {
        return $this->jsBehavior('toggle', [$this->getItemIdx($item)], $when);
    }

    /**
     * Return an accordion js behavior command
     * as in Semantic-ui behavior for Accordion module.
     * Ex: toggle an accordion from it's index value.
     * $accordion->jsBehavior('toggle', 1)
     *
     * @param string     $behavior   The name of the behavior for the module.
     * @param array      $args       The behaviors argument as an array.
     * @param bool       $when       When this js action is render.
     *
     * @return mixed
     */
    public function jsBehavior($behavior, $args, $when = null)
    {
        return $this->js($when)->accordion($behavior, ...$args);
    }

    /**
     * Return the index of an accordion item in collection.
     *
     * @param AccordionITem   $item
     *
     * @return int|string
     */
    private function getItemIdx($item)
    {
        $idx = -1;
        foreach ($this->items as $key => $accordion_item) {
            if ($accordion_item->name === $item->name) {
                $idx = $key;
                break;
            }
        }
        return $idx;
    }

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        if ($this->type) {
            $this->addClass($this->type);
        }

        $this->js(true)->accordion($this->settings);

        if ($this->activeItem > -1) {
            $this->jsBehavior('open', [$this->activeItem], true);
        }

        parent::renderView();
    }
}
