<?php

namespace atk4\ui;

/**
 * Class jsNotify
 */
class jsNotify implements jsExpressionable
{

    public $options = null;
    public $attachTo = null;

    public function __construct($options = null, $attachTo = null)
    {
        if ($options && is_array($options)) {
            $this->setOptions($options);
        }

        if ($attachTo) {
            $this->attachTo = $attachTo;
        }

    }

    /**
     * Set Notifier options using array.
     *
     * @param $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Set notifier option by specifying option name.
     *
     * @param $option
     * @param $value
     *
     * @return $this
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Set notifier content.
     *
     * @param $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->setOption('content', $content);

        return $this;
    }

    /**
     * Set notifier color.
     *  - any colors define in semantic-ui can be used.
     *
     * @param $color
     *
     * @return $this
     */
    public function setColor($color)
    {
        $this->setOption('color', $color);

        return $this;
    }

    /**
     * Add an icon to the notifier.
     *
     * @param $icon
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->setOption('icon', $icon);

        return $this;
    }

    /**
     * Set open and close transition for the notifier.
     *   - any transition define in semantic ui can be used.
     *
     * @param $openTransition
     * @param null $closeTransition
     *
     * @return $this
     */
    public function setTransition($openTransition, $closeTransition = null)
    {
        $this->setOption('openTransition', $openTransition);

        if ($closeTransition) {
            $this->setOption('closeTransition', $closeTransition);
        }

        return $this;
    }

    /**
     * Set open duration in millisecond.
     *
     * @param $duration : interger
     *
     * @return $this
     */
    public function setDuration($duration)
    {
        $this->setOption('duration', $duration);

        return $this;
    }

    /**
     * Set notifier position within the body of the page or within the attach element.
     *
     * @param $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->setOption('position', $position);

        return $this;
    }

    /**
     * Set the width percentage of the notifier within the body or attached to element.
     *
     * @param $width: integer
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->setOption('width', $width);

        return $this;
    }

    /**
     * Set the opacity of the notifier.
     *
     * @param $opacity : range from 0 to 1
     *
     * @return $this
     */
    public function setOpacity($opacity)
    {
        $this->setOption('opacity', $opacity);

        return $this;
    }

    /**
     * Attach this notifier to a view object.
     *  - position and width of notifier will be relative to this view object.
     *
     * note: notifier is attach to 'body' element by default.
     *
     * @param $to
     *
     * @throws Exception
     *
     * @return $this
     */
    public function attachTo($to)
    {
        if (!$to instanceof View) {
            throw new Exception('You need to attach notifier to a view!');
        }

        $this->attachTo = $to;

        return $this;
    }


    /**
     * Render the notifier.
     *
     * @return string
     */
    public function jsRender()
    {
        if ($this->attachTo) {
            $final = $this->attachTo->js();
        } else {
            $final = new jsChain();
        }

        $final->atkNotify($this->options);

        return $final->jsRender();
    }
}