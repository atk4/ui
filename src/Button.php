<?php

namespace atk4\ui;

/**
 * Component implementing UI Button.
 */
class Button extends View
{
    // @inheritdoc
    public $ui = 'button';

    /**
     * Icon that will appear on the button (left).
     * @var string|array|Icon
     */
    public $icon = null;

    /**
     * Additional icon that can appear on the right of the button.
     * @var [type]
     */
    public $rightIcon = null;

    /**
     * $icon property will end up a button icon.
     *
     * {@inheritdoc}
     */
    public function renderView()
    {
        if ($this->icon && !is_object($this->icon)) {
            $this->icon = $this->add(new Icon($this->icon), 'Content');

            if ($this->content) {
                $this->addClass('labeled');
                $this->add(new Text($this->content));
                $this->content = false;
            }

            $this->addClass('icon');
        }

        if ($this->rightIcon && !is_object($this->rightIcon)) {
            $this->rightIcon = $this->add(new Icon($this->rightIcon), 'Content');

            if ($this->content) {
                $this->addClass('right labeled');
                $this->add(new Text($this->content));
                $this->content = false;
            }

            $this->addClass('icon');
        }

        parent::renderView();
    }

    /**
     * Makes button into a "<a>" element with a link.
     *
     * @param string $url
     *
     * @return $this
     */
    public function link($url)
    {
        $this->element = 'a';
        if (is_string($url)) {
            $this->setAttr('href', $url);

            return $this;
        }
        $this->setAttr('href', $this->app->url($url));

        return $this;
    }

    /**
     * By Default buttons should have something written on them, e.g. "Button".
     *
     * {@inheritdoc}
     */
    public function recursiveRender()
    {
        parent::recursiveRender();
        if (!$this->template->get('Content')) {
            $this->template->set('Content', 'Button');
        }
    }
}
