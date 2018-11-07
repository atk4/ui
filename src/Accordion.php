<?php

namespace atk4\ui;

/**
 * Accordion is a View holding accordion items.
 * Each accordion item become clickable in order to hide or show it's content.
 *
 * You can add static content to an accordion item or set a callback
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
     * Add an accordion item.
     * You can add static View within your item or pass
     * a callback for dynamic content.
     *
     * @param $name
     * @param null $callback
     *
     * @throws Exception
     *
     * @return View
     */
    public function addItem($name, $callback = null)
    {
        $item = $this->add(['AccordionItem', 'title' => $name]);

        if ($callback) {
            $item->virtualPage = $item->add(['VirtualPage', 'ui' => '']);
            $item->virtualPage->set($callback);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        if ($this->type) {
            $this->addClass($this->type);
        }

        $settings = array_merge([
            'onOpening' => new jsFunction([new jsExpression('$(this).atkReloadView({uri:$(this).data("path"), uri_options:{json:1}})')]),
            ], $this->settings);

        $this->js(true)->accordion($settings);

        parent::renderView();
    }
}
