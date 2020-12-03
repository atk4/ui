<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;

/**
 * Wizard widget.
 */
class Wizard extends View
{
    use \Atk4\Core\SessionTrait;

    public $defaultTemplate = 'wizard.html';
    public $ui = 'steps';

    /**
     * Callback, that triggers selection of a step.
     *
     * @var Callback
     */
    public $stepCallback;

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
    public $currentStep;

    /**
     * Button for going to previous step.
     *
     * @var Button
     */
    public $buttonPrev;

    /**
     * Buttor for going to next step.
     *
     * @var Button
     */
    public $buttonNext;

    /**
     * Icon that will be used on all steps by default.
     *  - 'empty' , since no such icon exists, no visible icon will be used unless step is completed
     *  - 'square outline', use this (or any other) Semantic UI icon by default
     *  - false,  disables icons alltogether (or using checkboxes for completed steps).
     *
     * @var string|false
     */
    public $defaultIcon = 'empty'; // 'square outline'

    protected function init(): void
    {
        parent::init();

        if (!$this->stepCallback) {
            $this->stepCallback = Callback::addTo($this, ['urlTrigger' => $this->name]);
        }

        $this->currentStep = (int) ($this->stepCallback->getTriggeredValue() ?: 0);

        $this->stepTemplate = $this->template->cloneRegion('Step');
        $this->template->del('Step');

        // add buttons
        if ($this->currentStep) {
            $this->buttonPrev = Button::addTo($this, ['Back', 'basic'], ['Left']);
            $this->buttonPrev->link($this->stepCallback->getUrl((string) ($this->currentStep - 1)));
        }

        $this->buttonNext = Button::addTo($this, ['Next', 'primary'], ['Right']);
        $this->buttonFinish = Button::addTo($this, ['Finish', 'primary'], ['Right']);

        $this->buttonNext->link($this->stepCallback->getUrl((string) ($this->currentStep + 1)));
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
        $step = Factory::factory([
            Step::class,
            'wizard' => $this,
            'template' => clone $this->stepTemplate,
            'sequence' => count($this->steps),
        ], is_string($name) ? [$name] : $name);

        // add tabs menu item
        $this->steps[] = $this->add($step, 'Step');

        if (!$this->stepCallback->isTriggered()) {
            $_GET[$this->stepCallback->getUrlTrigger()] = '0';
        }

        if ($step->sequence === $this->currentStep) {
            $step->addClass('active');

            $this->stepCallback->set($callback, [$this]);
        } elseif ($step->sequence < $this->currentStep) {
            $step->addClass('completed');
        }

        if ($step->icon === null) {
            $step->icon = $this->defaultIcon;
        }

        return $step;
    }

    /**
     * Adds an extra screen to show user when he goes beyond last step.
     * There won't be "back" button on this step anymore.
     *
     * @param \Closure $callback Virtual page
     */
    public function addFinish(\Closure $callback)
    {
        if (count($this->steps) === $this->currentStep + 1) {
            $this->buttonFinish->link($this->stepCallback->getUrl((string) (count($this->steps))));
        } elseif ($this->currentStep === count($this->steps)) {
            $this->buttonPrev->destroy();
            $this->buttonNext->addClass('disabled')->set('Completed');
            $this->buttonFinish->destroy();

            $this->getApp()->catch_runaway_callbacks = false;
            $callback($this);
        } else {
            $this->buttonFinish->destroy();
        }
    }

    public function add($seed, $region = null): AbstractView
    {
        $result = parent::add($seed, $region);

        if ($result instanceof Form) {
            // mingle with the button icon
            if ($result->buttonSave !== null) {
                $result->buttonSave->destroy();
                $result->buttonSave = null;
            }

            $this->buttonNext->on('click', $result->js()->submit());
        }

        return $result;
    }

    /**
     * Get URL to next step. Will respect stickyGET.
     *
     * @return string URL to next step
     */
    public function urlNext()
    {
        return $this->stepCallback->getUrl((string) ($this->currentStep + 1));
    }

    /**
     * Get URL to previous step. Will respect stickyGET.
     *
     * @return string URL to previous step
     */
    public function jsNext()
    {
        return new JsExpression('document.location = []', [$this->urlNext()]);
    }

    protected function recursiveRender(): void
    {
        if (!$this->steps) {
            $this->addStep(['No Steps Defined', 'icon' => 'configure', 'description' => 'use $wizard->addStep() now'], function ($p) {
                Message::addTo($p, ['Step content will appear here', 'type' => 'error', 'text' => 'Specify callback to addStep() which would populate this area.']);
            });
        }

        if (count($this->steps) === $this->currentStep + 1) {
            $this->buttonNext->destroy();
        }

        parent::recursiveRender();
    }

    protected function renderView(): void
    {
        // Set proper width to the wizard
        $c = count($this->steps);
        $enumeration = ['one', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'];
        $this->ui = $enumeration[$c] . ' ' . $this->ui;

        if ($c > 6) {
            $this->addClass('mini');
        } elseif ($c > 5) {
            $this->addClass('tiny');
        } elseif ($c > 4) {
            $this->addClass('small');
        }

        parent::renderView();
    }

    protected function mergeStickyArgsFromChildView(): ?AbstractView
    {
        return $this->stepCallback;
    }
}
