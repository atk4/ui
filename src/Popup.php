<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;

/**
 * Implement popup view.
 *
 * Popup are views that will be display when triggered by another view.
 *
 * Popup can add content statically or dynamically via a callback.
 *
 * When adding a popup to the page, you need to specify it's trigger element
 * and the event needed on the trigger element in order to display the popup.
 */
class Popup extends View
{
    public $ui = 'popup';

    /**
     * The view activating the popup.
     * Usually the view where popup is attached to,
     * unless target is supply.
     *
     * @var View|string|null object view or a string id
     */
    public $triggerBy;

    /** @var string Js event that trigger the popup. */
    public $triggerOn;

    /** @var string Default position of the popup in relation to target element. */
    public $position = 'top left';

    /**
     * When set to false, target is the triggerBy element.
     * Otherwise, you can supply a View object where popup will be shown.
     *
     * @var View|false
     */
    public $target = false;

    /** @var array Popup options as defined in Fomantic-UI popup module. */
    public $popOptions = [];

    /** @var Callback|null The callback use to generate dynamic content. */
    public $cb;

    /**
     * The dynamic View to load inside the popup
     * when dynamic content is use.
     *
     * @var View|array
     */
    public $dynamicContent = [View::class];

    /**
     * Whether or not dynamic content is cache.
     * If cache is on, will retrieve content only the first time popup is required.
     *
     * @var bool
     */
    public $useCache = false;

    /** @var string Min width for a dynamic popup. */
    public $minWidth;

    /** @var string Min height for a dynamic popup. */
    public $minHeight;

    /**
     * Whether or not the click event triggering popup
     * should stop event propagation.
     *
     * Ex: when Popup is located inside a sortable grid header.
     * Set this options to true in order to activate just the popup
     * and stop sort action.
     *
     * @var bool
     */
    public $stopClickEvent = false;

    /**
     * @param View|array<string, mixed> $triggerBy
     */
    public function __construct($triggerBy = [])
    {
        if (is_object($triggerBy)) {
            $triggerBy = ['triggerBy' => $triggerBy];
        }

        parent::__construct($triggerBy);
    }

    protected function init(): void
    {
        parent::init();

        if ($this->triggerOn === null) {
            if ($this->triggerBy instanceof Menu
                || $this->triggerBy instanceof MenuItem
                || $this->triggerBy instanceof Dropdown
            ) {
                $this->triggerOn = 'hover';
            } elseif ($this->triggerBy instanceof Button) {
                $this->triggerOn = 'click';
            }
        }

        $this->popOptions = array_merge($this->popOptions, [
            'popup' => $this,
            'on' => $this->triggerOn,
            'position' => $this->position,
            'target' => $this->target,
        ]);
    }

    /**
     * Set callback for loading content dynamically.
     * Callback will receive a view attach to this popup
     * for adding content to it.
     *
     * @param \Closure(View): void $fx
     *
     * @return $this
     */
    public function set($fx = null)
    {
        if (!$fx instanceof \Closure) {
            throw new \TypeError('$fx must be of type Closure');
        } elseif ('func_num_args'() > 1) {
            throw new Exception('Only one argument is needed by Popup::set()');
        }

        $this->cb = Callback::addTo($this);

        if (!$this->minWidth) {
            $this->minWidth = '80px';
        }

        if (!$this->minHeight) {
            $this->minHeight = '45px';
        }

        // create content view to pass to callback
        $content = $this->add($this->dynamicContent);
        $this->cb->set($fx, [$content]);
        // only render our content view
        // PopupService will replace content with this one
        $this->cb->terminateJson($content);

        return $this;
    }

    /**
     * @param View|string $trigger
     *
     * @return $this
     */
    public function setTriggerBy($trigger)
    {
        $this->triggerBy = $trigger;

        return $this;
    }

    /**
     * Allow to pass a target selector by name, i.e. a CSS class name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setTargetByName($name)
    {
        $this->popOptions['target'] = $name;

        return $this;
    }

    /**
     * Whether popup stay open when user hover on it or not.
     *
     * @return $this
     */
    public function setHoverable(bool $isOverable = true)
    {
        $this->popOptions['hoverable'] = $isOverable;

        return $this;
    }

    /**
     * Set a popup options as defined in Fomantic-UI popup module.
     *
     * @param mixed $option
     *
     * @return $this
     */
    public function setOption(string $name, $option)
    {
        $this->popOptions[$name] = $option;

        return $this;
    }

    /**
     * Return JS action need to display popup.
     * When a grid is reloading, this method can be call
     * in order to display the popup once again.
     *
     * @return Jquery
     */
    public function jsPopup(): JsExpressionable
    {
        $selector = $this->triggerBy;
        if ($this->triggerBy instanceof Form\Control) {
            $selector = '#' . $this->triggerBy->name . '_input';
        }
        $chain = new Jquery($selector);
        $chain->popup($this->popOptions);
        if ($this->stopClickEvent) {
            $chain->on('click', new JsExpression('function (e) { e.stopPropagation(); }'));
        }

        return $chain;
    }

    protected function renderView(): void
    {
        if ($this->triggerBy) {
            $this->js(true, $this->jsPopup());
        }

        if ($this->cb) {
            $this->setAttr('data-url', $this->cb->getJsUrl());
            $this->setAttr('data-cache', $this->useCache ? 'true' : 'false');
        }

        if ($this->minWidth) {
            $this->setStyle('min-width', $this->minWidth);
        }

        if ($this->minHeight) {
            $this->setStyle('min-height', $this->minHeight);
        }

        parent::renderView();
    }
}
