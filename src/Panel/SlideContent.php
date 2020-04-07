<?php
/**
 * Slide Panel Content.
 */

namespace atk4\ui\Panel;

use atk4\ui\jQuery;
use atk4\ui\jsCallback;
use atk4\ui\View;

class SlideContent extends View implements SlidableContent
{
    public $defaultTemplate = 'panel/slide-content.html';
    public $cb = null;

    protected $warningSelector;
    protected $warningTrigger;

    protected $closeSelector;

    public function init()
    {
        parent::init();
        $this->addClass('atk-slide-content');
        $this->setCb(new jsCallback(['appSticky' => true]));
        $this->setWarningSelector('.atk-slide-content-warning');
        $this->setWarningTrigger('atk-visible');
        $this->setCloseSelector('.atk-slide-close');
    }

    public function getWarningSelector() :string
    {
        return $this->warningSelector;
    }

    public function getCallbackUrl() :string
    {
        return $this->cb->getJSURL();
    }

    public function getWarningTrigger() :string
    {
        return $this->warningTrigger;
    }

    public function setWarningSelector(string $selector)
    {
        $this->warningSelector = $selector;
    }

    public function setWarningTrigger(string $trigger)
    {
        $this->warningTrigger = $trigger;
    }

    public function setCb(jsCallback $cb)
    {
        $this->cb = $this->add($cb);
    }

    public function setCloseSelector(string $selector)
    {
        $this->closeSelector = $selector;
    }

    public function getCloseSelector(): string
    {
        return $this->closeSelector;
    }

    /**
     * Will load content into callback.
     * Callable will receive this view as first parameter.
     *
     * @param $callback
     */
    public function onLoad($callback)
    {
        $this->cb->set(function() use($callback) {
            if ($this->cb->triggered()) {
                call_user_func($callback, $this);
                $this->app->terminateJSON($this);
            }
        });
    }

    /**
     * Return an array of css selector where content will be
     * cleared on reload.
     *
     * @return array
     */
    public function getClearSelector() :array
    {
        return ['.atk-slide-content'];
    }

    /**
     * Display or not a Warning sign in Flyout.
     *
     * @param bool   $state
     * @param string $selector
     *
     * @return jQuery
     */
    public function jsDisplayWarning(bool $state = true )
    {
        $chain = new jQuery('#' . $this->name . ' ' . $this->getWarningSelector());

        return $state ? $chain->addClass($this->getWarningTrigger()) : $chain->removeClass($this->getWarningTrigger());
    }

}
