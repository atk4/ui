<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Wizard widget.
 */
class Wizard extends View
{
    use \atk4\core\SessionTrait;

    public $defaultTemplate = 'wizard.html';
    public $ui = 'steps';

    /**
     * Callback, that triggers selection of a step.
     *
     * @var callable
     */
    public $stepCallback = null;

    /**
     * List of steps.
     *
     * @var array
     */
    public $steps = [];

    /**
     * Current step.
     *
     * @var int
     */
    public $currentStep = null;

    /**
     * Button for going to previous step.
     *
     * @var Button
     */
    public $buttonPrev = null;

    /**
     * Buttor for going to next step.
     *
     * @var Button
     */
    public $buttonNext = null;

    /**
     * Icon that will be used on all steps by default.
     *  - 'empty' , since no such icon exists, no visible icon will be used unless step is completed
     *  - 'square outline', use this (or any other) Semantic UI icon by default
     *  - false,  disables icons alltogether (or using checkboxes for completed steps).
     *
     * @var string|false
     */
    public $defaultIcon = 'empty'; // 'square outline'

    public function init()
    {
        parent::init();
        $this->stepCallback = $this->add(['Callback', 'urlTrigger'=>$this->name]);

        $this->currentStep = $this->stepCallback->triggered() ?: 0;

        $this->stepTemplate = $this->template->cloneRegion('Step');
        $this->template->del('Step');

        // add buttons
        if ($this->currentStep) {
            $this->buttonPrev = $this->add(['Button', 'Back', 'basic'], 'Left');
            $this->buttonPrev->link($this->stepCallback->getURL($this->currentStep - 1));
        }

        $this->buttonNext = $this->add(['Button', 'Next', 'primary'], 'Right');
        $this->buttonFinish = $this->add(['Button', 'Finish', 'primary'], 'Right');

        $this->buttonNext->link($this->stepCallback->getURL($this->currentStep + 1));
    }

    /**
     * Adds step to the wizard.
     *
     * @param mixed $name     Name of tab or Tab object
     * @param mixed $callback Optional callback action or URL (or array with url + parameters)
     *
     * @return View
     */
    public function addStep($name, $callback)
    {
        $step = $this->factory([
            'Step',
            'wizard'  => $this,
            'template'=> clone $this->stepTemplate,
            'sequence'=> count($this->steps),
        ], $name);

        // add tabs menu item
        $this->steps[] = $this->add($step, 'Step');

        if (!$this->stepCallback->triggered()) {
            $_GET[$this->stepCallback->urlTrigger] = 0;
        }

        if ($step->sequence == $this->currentStep) {
            $step->addClass('active');

            $this->stepCallback->set($callback, [$this]);
        } elseif ($step->sequence < $this->currentStep) {
            $step->addClass('completed');
        }

        if ($step->icon == null) {
            $step->icon = $this->defaultIcon;
        }

        return $step;
    }

    /**
     * Adds an extra screen to show user when he goes beyond last step.
     * There won't be "back" button on this step anymore.
     *
     * @param callable $callback Virtual page
     */
    public function addFinish($callback)
    {
        if (count($this->steps) == $this->currentStep + 1) {
            $this->buttonFinish->link($this->stepCallback->getURL(count($this->steps)));
        } elseif ($this->currentStep == count($this->steps)) {
            $this->buttonPrev->destroy();
            $this->buttonNext->addClass('disabled')->set('Completed');
            $this->buttonFinish->destroy();

            $this->app->catch_runaway_callbacks = false;
            call_user_func($callback, $this);
        } else {
            $this->buttonFinish->destroy();
        }
    }

    public function add($seed, $region = null)
    {
        $result = parent::add($seed, $region);

        if ($result instanceof Form) {
            // mingle with the button icon
            $result->buttonSave->destroy();
            $result->buttonSave = null;

            $this->buttonNext->on('click', $result->js()->submit());
        }

        return $result;
    }

    /**
     * Get URL to next step. Will respect stickyGET.
     *
     * @return string URL to next step.
     */
    public function urlNext()
    {
        return $this->stepCallback->getURL($this->currentStep + 1);
    }

    /**
     * Get URL to previous step. Will respect stickyGET.
     *
     * @return string URL to previous step.
     */
    public function jsNext()
    {
        return new jsExpression('document.location = []', [$this->urlNext()]);
    }

    public function recursiveRender()
    {
        if (!$this->steps) {
            $this->addStep(['No Steps Defined', 'icon'=>'configure', 'description'=>'use $wizard->addStep() now'], function ($p) {
                $p->add(['Message', 'Step content will appear here', 'type'=>'error', 'text'=>'Specify callback to addStep() which would populate this area.']);
            });
        }

        if (count($this->steps) == $this->currentStep + 1) {
            $this->buttonNext->destroy();
        }

        parent::recursiveRender();
    }

    public function renderView()
    {
        // Set proper width to the wizard
        $c = count($this->steps);
        $enumeration = ['one', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'];
        $this->ui = $enumeration[$c].' '.$this->ui;

        if ($c > 6) {
            $this->addClass('mini');
        } elseif ($c > 5) {
            $this->addClass('tiny');
        } elseif ($c > 4) {
            $this->addClass('small');
        }

        parent::renderView();
    }
}
