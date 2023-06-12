<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsExpressionable;

/**
 * Accordion is a View holding accordion sections.
 *
 * You can add static content to an accordion section or pass a callback
 * for adding content dynamically.
 */
class Accordion extends View
{
    public $defaultTemplate = 'accordion.html';

    public $ui = 'accordion';

    /** @var array|string|null The CSS class for Fomantic-UI accordion type. */
    public $type;

    /** @var array Settings as per Fomantic-UI accordion settings. */
    public $settings = [];

    /** @var array A collection of AccordionSection in this Accordion. */
    public $sections = [];

    /** @var int The AccordionSection index number to activate on load. */
    public $activeSection = -1;

    /**
     * Add an accordion section.
     * You can add static View within your section or pass
     * a callback for dynamic content.
     *
     * @param string                                                                                            $title
     * @param \Closure(VirtualPage, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed): void $callback
     * @param string                                                                                            $icon
     *
     * @return AccordionSection
     */
    public function addSection($title, \Closure $callback = null, $icon = 'dropdown')
    {
        $section = AccordionSection::addTo($this, ['title' => $title, 'icon' => $icon]);

        // if there is callback action, then use VirtualPage
        if ($callback) {
            $section->virtualPage = VirtualPage::addTo($section, ['ui' => '']);
            $section->virtualPage->set($callback);
        }

        $this->sections[] = $section;

        return $section;
    }

    /**
     * Activate or open an accordion section.
     *
     * @param AccordionSection $section the section to activate
     */
    public function activate($section): void
    {
        $this->activeSection = $this->getSectionIdx($section);
    }

    /**
     * @param AccordionSection $section
     * @param bool             $when
     *
     * @return JsChain
     */
    public function jsOpen($section, $when = false): JsExpressionable
    {
        return $this->jsBehavior('open', [$this->getSectionIdx($section)], $when);
    }

    /**
     * @param bool $when
     *
     * @return JsChain
     */
    public function jsCloseOthers($when = false): JsExpressionable
    {
        return $this->jsBehavior('close others', [], $when);
    }

    /**
     * @param AccordionSection $section
     * @param bool             $when
     *
     * @return JsChain
     */
    public function jsClose($section, $when = false): JsExpressionable
    {
        return $this->jsBehavior('close', [$this->getSectionIdx($section)], $when);
    }

    /**
     * @param AccordionSection $section
     * @param bool             $when
     *
     * @return JsChain
     */
    public function jsToggle($section, $when = false): JsExpressionable
    {
        return $this->jsBehavior('toggle', [$this->getSectionIdx($section)], $when);
    }

    /**
     * Return an accordion JS behavior command
     * as in Fomantic-UI behavior for Accordion module.
     * Ex: toggle an accordion from it's index value.
     * $accordion->jsBehavior('toggle', 1).
     *
     * @param string $behavior the name of the behavior for the module
     * @param bool   $when
     *
     * @return JsChain
     */
    public function jsBehavior($behavior, array $args, $when = false): JsExpressionable
    {
        return $this->js($when)->accordion($behavior, ...$args);
    }

    /**
     * Return the index of an accordion section in collection.
     *
     * @return int
     */
    public function getSectionIdx(AccordionSection $section)
    {
        $idx = -1;
        foreach ($this->sections as $k => $v) {
            if ($v->name === $section->name) {
                $idx = $k;

                break;
            }
        }

        return $idx;
    }

    protected function renderView(): void
    {
        if ($this->type) {
            $this->addClass($this->type);
        }

        // initialize top accordion only, otherwise nested accordion won't work
        // https://github.com/fomantic/Fomantic-UI/issues/254
        if ($this->getClosestOwner(AccordionSection::class) === null) {
            $this->js(true)->accordion($this->settings);
        }

        if ($this->activeSection > -1) {
            $this->jsBehavior('open', [$this->activeSection], true);
        }

        parent::renderView();
    }
}
