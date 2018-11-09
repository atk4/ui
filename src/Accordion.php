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
     * A collection of AccordionSection in this Accordion;.
     *
     * @var array
     */
    public $sections = [];

    /**
     * The AccordionItem index number to activate on load.
     *
     * @var int
     */
    public $activeSection = -1;

    /**
     * Add an accordion item.
     * You can add static View within your item or pass
     * a callback for dynamic content.
     *
     * @param string        $title
     * @param null|callable $callback
     * @param string        $icon
     *
     * @throws Exception
     *
     * @return View
     */
    public function addItem($title, $callback = null, $icon = 'dropdown')
    {
        $section = $this->add(['AccordionSection', 'title' => $title, 'icon' => $icon]);

        if ($callback) {
            $section->virtualPage = $section->add(['VirtualPage', 'ui' => '']);
            $section->virtualPage->set($callback);
        }

        $this->sections[] = $section;

        return $section;
    }

    /**
     * Activate or open an accordion item.
     *
     * @param AccordionSection $section The item to activate.
     */
    public function activate($section)
    {
        $this->activeSection = $this->getSectionIdx($section);
    }

    /*
     * JS Behavior wrapper functions.
     */
    public function jsRefresh($when = null)
    {
        return $this->jsBehavior('refresh', [], $when);
    }

    public function jsOpen($section, $when = null)
    {
        return $this->jsBehavior('open', [$this->getSectionIdx($section)], $when);
    }

    public function jsCloseOthers($when = null)
    {
        return $this->jsBehavior('close others', [], $when);
    }

    public function jsClose($section, $when = null)
    {
        return $this->jsBehavior('close', [$this->getSectionIdx($section)], $when);
    }

    public function jsToggle($section, $when = null)
    {
        return $this->jsBehavior('toggle', [$this->getSectionIdx($section)], $when);
    }

    /**
     * Return an accordion js behavior command
     * as in Semantic-ui behavior for Accordion module.
     * Ex: toggle an accordion from it's index value.
     * $accordion->jsBehavior('toggle', 1).
     *
     * @param string $behavior The name of the behavior for the module.
     * @param array  $args     The behaviors argument as an array.
     * @param bool   $when     When this js action is render.
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
     * @param AccordionSection $section
     *
     * @return int|string
     */
    private function getSectionIdx($section)
    {
        $idx = -1;
        foreach ($this->sections as $key => $accordion_section) {
            if ($accordion_section->name === $section->name) {
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

        if ($this->activeSection > -1) {
            $this->jsBehavior('open', [$this->activeSection], true);
        }

        parent::renderView();
    }
}
