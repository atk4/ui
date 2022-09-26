<?php

declare(strict_types=1);

namespace Atk4\Ui;

class JsNotify implements JsExpressionable
{
    use \Atk4\Core\DiContainerTrait;

    public array $options = [];

    /** @var View|null */
    public $attachTo;

    /**
     * @param string|array $options
     * @param View         $attachTo
     */
    public function __construct($options = null, View $attachTo = null)
    {
        if (is_string($options)) {
            $this->setContent($options);
        } elseif (is_array($options)) {
            $this->setDefaults($options);
        }

        if ($attachTo) {
            $this->attachTo = $attachTo;
        }
    }

    /**
     * Set notify message.
     *
     * @param string $msg
     *
     * @return $this
     */
    public function setMessage($msg)
    {
        return $this->setContent($msg);
    }

    /**
     * Set notifier content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->options['content'] = $content;

        return $this;
    }

    /**
     * Set notifier color.
     *  - any colors define in Fomantic-UI can be used.
     *
     * @param string $color
     *
     * @return $this
     */
    public function setColor($color)
    {
        $this->options['color'] = $color;

        return $this;
    }

    /**
     * Add an icon to the notifier.
     *
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->options['icon'] = $icon;

        return $this;
    }

    /**
     * Set open and close transition for the notifier.
     *   - any transition define in Fomantic-UI can be used.
     *
     * @param string      $openTransition
     * @param string|null $closeTransition
     *
     * @return $this
     */
    public function setTransition($openTransition, $closeTransition = null)
    {
        $this->options['openTransition'] = $openTransition;

        if ($closeTransition) {
            $this->options['closeTransition'] = $closeTransition;
        }

        return $this;
    }

    /**
     * Set open duration in millisecond.
     *  - if you set duration to 0, then notification
     *    will stay forever until close by user.
     *
     * @param float|int $duration
     *
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->options['duration'] = $duration;

        return $this;
    }

    /**
     * Set notifier position within the body of the page or within the attach element.
     *
     * @param string $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->options['position'] = $position;

        return $this;
    }

    /**
     * Set the width percentage of the notifier within the body or attached to element.
     *
     * @param string $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->options['width'] = $width;

        return $this;
    }

    /**
     * Set the opacity of the notifier.
     *
     * @param float $opacity Range from 0.0 to 1.0
     *
     * @return $this
     */
    public function setOpacity(float $opacity)
    {
        $this->options['opacity'] = $opacity;

        return $this;
    }

    /**
     * Attach this notifier to a view object.
     *  - position and width of notifier will be relative to this view object.
     *
     * Note: notifier is attach to 'body' element by default.
     *
     * @return $this
     */
    public function attachTo(View $to)
    {
        $this->attachTo = $to;

        return $this;
    }

    public function jsRender(): string
    {
        if ($this->attachTo) {
            $final = $this->attachTo->js();
        } else {
            $final = new Jquery();
        }

        $final->atkNotify($this->options);

        return $final->jsRender();
    }
}
