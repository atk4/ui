<?php

namespace atk4\ui;

/**
 * Implement popup view.
 *
 * Popup are views that will be display when triggered by another view.
 *
 * Popup can add content statically or dynamically via a callback.
 *
 * When adding a popup to the page, you need to specify it's trigger element
 * and the event needed on the trigger element in order to display the popup.
 *
 */
class Popup extends View
{
    public $ui = 'popup';

    /**
     * The view activating the popup.
     * Usually the view where popup is attached to,
     * unless target is supply.
     *
     * @var View|string|null Object view or a string id.
     */
    public $triggerBy = null;

    /**
     * Js event that trigger the popup.
     *
     * @var string
     */
    public $triggerOn = 'hover';

    /**
     * Default position of the popup in relation to target element.
     *
     * @var string
     */
    public $position = 'top left';

    /**
     * When set to false, target is the triggerBy element.
     * Otherwise, you can supply a View object where popup will be shown.
     *
     * @var View|bool
     */
    public $target = false;

    /**
     * Popup options as defined in semantic-ui popup module.
     *
     * @var array
     */
    public $popOptions = [];

    /**
     * The callback use to generate dynamic content.
     *
     * @var Callback|null
     */
    public $cb = null;

    /**
     * The dynamic View to load inside the popup
     * when dynamic content is use.
     *
     * Default to 'View'.
     *
     * @var View|string
     */
    public $dynamicContent = 'View';

    /**
     * Whether or not dynamic content is cache.
     * If cache is on, will retrieve content only the first time
     * popup is requrired.
     *
     * @var bool
     */
    public $useCache = false;

    /**
     * Min width for a dynamic popup.
     *
     * @var string
     */
    public $minWidth = '120px';

    /**
     * Min height for a dynamic popup.
     *
     * @var string
     */
    public $minHeight = '60px';

    public function init()
    {
        parent::init();
        $this->popOptions = array_merge($this->popOptions, [
            'popup'    => '#'.$this->name,
            'on'       => $this->triggerOn,
            'position' => $this->position,
            'target'   => ($this->target) ? '#'.$this->target->name : false,
        ]);
    }

    /**
     * Set callback for loading content dynamically.
     * Callback will reveive a view attach to this popup
     * for adding content to it.
     *
     * @param $fx
     *
     * @throws Exception
     */
    public function set($fx)
    {
        if (!is_object($fx) && !($fx instanceof Closure)) {
            throw new Exception('Error: Need to pass a function to Popup::set()');
        }
        $this->cb = $this->add('Callback');

        if ($this->cb->triggered()) {
            //create content view to pass to callback.
            $content = $this->add($this->dynamicContent);
            $this->cb->set($fx, [$content]);
            //only render our content view.
            //PopupService will replace content with this one.
            $this->app->terminate($content->renderJSON());
        }

    }

    /**
     * Set triggerBy.
     *
     * @param $trigger
     *
     * @return $this
     */
    public function setTriggerBy($trigger)
    {
        $this->triggerBy = $trigger;

        return $this;
    }

    /**
     * Allow to pass a target selector by name, i.e. a css class name.
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
     * @param bool $isOverable
     *
     * @return $this
     */
    public function setHoverable($isOverable = true)
    {
        $this->popOptions['hoverable'] = $isOverable;

        return $this;
    }

    /**
     * Set a popup options as defined in semantic-ui popup module.
     *
     * @param $name
     * @param $option
     *
     * @return $this
     */
    public function setOption($name, $option)
    {
        $this->popOptions[$name] = $option;

        return $this;
    }

    /**
     * Setting options using using an array.
     *
     * @param $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->popOptions = array_merge($this->popOptions, $options);

        return $this;
    }

    public function renderView()
    {
        if ($this->triggerBy) {
            $name = $this->triggerBy;
            if (!is_string($this->triggerBy)) {
                $name = '#'.$this->triggerBy->name;
                if ($this->triggerBy instanceof FormField\Generic) {
                    $name = '#'.$this->triggerBy->name.'_input';
                }
            }
            $chain = new jQuery($name);
            $chain->popup($this->popOptions);
            $this->js(true, $chain);
        }

        if ($this->cb) {
            $this->setAttr('data-uri', $this->cb->getJSURL());
            $this->setAttr('data-cache', $this->useCache ? 'true':'false');
            $this->setStyle(['min-width' => $this->minWidth, 'min-height' => $this->minHeight]);
        }

        parent::renderView();
    }
}
