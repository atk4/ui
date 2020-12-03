<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Class JsNotify.
 */
class JsNotify implements JsExpressionable
{
    use \Atk4\Core\DiContainerTrait;

    public $options = [];
    public $attachTo;

    public function __construct($options = null, $attachTo = null)
    {
        if (is_array($options)) {
            $this->setDefaults($options);
        } elseif (is_string($options)) {
            $this->setDefaults(['content' => $options]);
        }

        if ($attachTo) {
            $this->attachTo = $attachTo;
        }
    }

    /**
     * Set notify message.
     *
     * @return $this
     */
    public function setMessage($msg)
    {
        return $this->setContent($msg);
    }

    /**
     * Set notifier option by specifying option name.
     */
    public function setMissingProperty(string $propertyName, $value): void
    {
        $this->options[$propertyName] = $value;
    }

    /**
     * Set notifier content.
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
     *  - any colors define in semantic-ui can be used.
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
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->options['icon'] = $icon;

        return $this;
    }

    /**
     * Set open and close transition for the notifier.
     *   - any transition define in semantic ui can be used.
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
     * @param float $opacity Range from 0 to 1
     *
     * @return $this
     */
    public function setOpacity($opacity)
    {
        $this->options['opacity'] = $opacity;

        return $this;
    }

    /**
     * Attach this notifier to a view object.
     *  - position and width of notifier will be relative to this view object.
     *
     * note: notifier is attach to 'body' element by default.
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

    public function jsRender(): string
    {
        if ($this->attachTo) {
            $final = $this->attachTo->js();
        } else {
            $final = new JsChain();
        }

        $final->atkNotify($this->options);

        return $final->jsRender();
    }
}
