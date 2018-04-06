<?php

namespace atk4\ui;

class Popup extends View
{
    public $ui = 'popup';

    /**
     * The view activating the popup.
     * Usually the view where popup is attached to,
     * unless target is supply.
     *
     * @var View|null
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


    public function init()
    {
        parent::init();
        $this->popOptions = array_merge($this->popOptions, [
            'popup' => '#'.$this->name,
            'on' => $this->triggerOn,
            'position' => $this->position,
            'target' => ($this->target) ? '#'.$this->target->name : false,
        ]);
    }

    public function setTriggerByName($name)
    {
        $this->popOptions['popup'] = $name;
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
            $name = '#'.$this->triggerBy->name;
            if ($this->triggerBy instanceof FormField\Generic) {
                $name = '#'.$this->triggerBy->name.'_input';
            }
            $chain = new jQuery($name);
            $chain->popup($this->popOptions);
            $this->js(true, $chain);
        }

        parent::renderView();
    }
}
