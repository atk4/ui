<?php

namespace atk4\ui;

/**h
 * This class add modal dialog to a page.
 *
 * Modal are added to the layout but their content is hidden by default.
 * $modal->show() is the triggered needed to actually display the modal.
 *
 * Modal can be use as a regular view, simply by adding other view to it.
 *  $modal->add(['Message', 'title'=>'Welcome to Agile Toolkit')->text('Your text here').
 *
 * Modal can add content dynamically via CallbackLater.
 *  $modal->set(function ($modal) {
 *     $modal->add('Form');
 * });
 *
 * Modal can use semantic-ui predefine method onApprove or onDeny by passing
 * a jsAction to Modal::addDenyAction or Modal::addApproveAction method. It will not close until the jsAction return true.
 *  $modal->addDenyAction('No', new \atk4\ui\jsExpression('function(){window.alert("Can\'t do that."); return false;}'));
 *  $modal->addApproveAction('Yes', new \atk4\ui\jsExpression('function(){window.alert("You\'re good to go!");}'));
 *
 * You may also prevent modal from closing via the esc or dimmed area click using $modal->notClosable().
 *
 * Some helper methods are also available to set: transition time, transition type or modal settings from semantic-ui.
 */
class Modal extends View
{
    public $defaultTemplate = 'modal.html';

    protected $hasBeenRendered = false;

    /**
     * Set to empty or false for no header.
     *
     * @var string
     */
    public $title = 'Modal title';
    public $loading_label = 'Loading...';
    public $headerCSS = 'header';
    public $ui = 'modal';
    public $fx = [];
    public $cb = null;
    public $cb_view = null;
    public $args = [];

    //now only supported json type response.
    public $type = 'json';

    /**
     * Add ability to add css classes to "content" div.
     *
     * @var array
     */
    public $contentCSS = ['img', 'content', 'atk-dialog-content'];

    /*
     * if true, the <div class="actions"> at the bottom of the modal is
     * shown. Automatically set to true if any actions are added
     *
     * @var bool
     */
    public $showActions = false;

    /**
     * Set callback function for this modal.
     *
     * @param array|string $fx
     * @param array|string $arg2
     *
     * @throws Exception
     *
     * @return $this
     */
    public function set($fx = [], $arg2 = null)
    {
        if (!is_object($fx) && !($fx instanceof Closure)) {
            throw new Exception('Error: Need to pass a function to Modal::set()');
        }
        $this->fx = [$fx];
        $this->enableCallback();

        return $this;
    }

    /**
     * Add View to be loaded in this modal and
     * attach CallbackLater to it.
     * The cb_view only will be loaded dynamically within modal
     * div.atk-content.
     */
    public function enableCallback()
    {
        $this->cb_view = $this->add('View');
        $this->cb_view->stickyGet('__atk_m', $this->name);
        $this->cb = $this->cb_view->add('CallbackLater');

        $this->cb->set(function () {
            if ($this->cb->triggered() && $this->fx) {
                $this->fx[0]($this->cb_view);
            }
            $modalName = isset($_GET['__atk_m']) ? $_GET['__atk_m'] : null;
            if ($modalName === $this->name) {
                $this->app->terminate($this->cb_view->renderJSON());
            }
        });
    }

    /**
     * Add CSS classes to "content" div.
     */
    public function addContentCSS($class)
    {
        if (is_string($class)) {
            $this->contentCSS = array_merge($this->contentCSS, [$class]);
        } elseif (is_array($class)) {
            $this->contentCSS = array_merge($this->contentCSS, $class);
        }
    }

    /**
     * Set modal to show on page.
     * Will trigger modal to be show on page.
     * ex: $button->on('click', $modal->show());.
     *
     * @param array $args
     *
     * @return mixed
     */
    public function show($args = [])
    {
        $js_chain = $this->js();
        if (!empty($args)) {
            $js_chain->data(['args' => $args]);
        }

        return $js_chain->modal('show');
    }

    /**
     * Hide modal from page.
     *
     * @return mixed
     */
    public function hide()
    {
        return $this->js()->modal('hide');
    }

    /**
     * Set modal option.
     *
     * @param $option
     * @param $value
     *
     * @return $this
     */
    public function setOption($option, $value)
    {
        $this->options['modal_option'][$option] = $value;

        return $this;
    }

    /**
     * Set modal options passing an array.
     *
     * @param $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        if (isset($this->options['modal_option'])) {
            $this->options['modal_option'] = array_merge($this->options['modal_option'], $options);
        } else {
            $this->options['modal_option'] = $options;
        }

        return $this;
    }

    /**
     * Whether any change in modal DOM should automatically refresh cached positions.
     * Allow modal window to add scrolling when adding content dynamically after modal creation.
     *
     * @return $this
     */
    public function observeChanges()
    {
        $this->setOptions(['observeChanges' => true]);

        return $this;
    }

    /**
     * Add scrolling capability to modal.
     *
     * @return $this
     */
    public function addScrolling()
    {
        $this->addContentCSS('scrolling');

        return $this;
    }

    /**
     * Set modal transition.
     *
     * @param $transition_type
     *
     * @return $this
     */
    public function transition($transition_type)
    {
        $this->settings('transition', $transition_type);

        return $this;
    }

    /**
     * Set modal transition duration.
     *
     * @param $time
     *
     * @return $this
     */
    public function duration($time)
    {
        $this->settings('duration', $time);

        return $this;
    }

    /**
     * Add modal settings.
     *
     * @param $setting_option
     * @param $value
     */
    public function settings($setting_option, $value)
    {
        $this->options['setting'][$setting_option] = $value;
    }

    /**
     * Add a deny action to modal.
     *
     * @param $label.
     * @param $jsAction : Javascript action that will run when deny is click.
     *
     * @return $this
     */
    public function addDenyAction($label, $jsAction)
    {
        $b = new Button();
        $b->set($label)->addClass('red cancel');
        $this->addButtonAction($b);
        $this->options['modal_option']['onDeny'] = $jsAction;

        return $this;
    }

    /**
     * Add an approve action button to modal.
     *
     * @param $label.
     * @param $jsAction : Javascript action that will run when approve is click.
     *
     * @return $this
     */
    public function addApproveAction($label, $jsAction)
    {
        $b = new Button();
        $b->set($label)->addClass('green ok');
        $this->addButtonAction($b);
        $this->options['modal_option']['onApprove'] = $jsAction;

        return $this;
    }

    /**
     * Add an action button to modal.
     *
     * @param $button
     *
     * @return $this
     */
    public function addButtonAction($button)
    {
        $this->add($button, 'actions');
        $this->showActions = true;

        return $this;
    }

    /**
     * Make this modal not closable via close icon, esc key or via the dimmer area.
     *
     * @return $this
     */
    public function notClosable()
    {
        $this->options['modal_option']['closable'] = false;

        return $this;
    }

    public function renderView()
    {
        $data['type'] = $this->type;
        $data['label'] = $this->loading_label;

        if (!empty($this->title)) {
            $this->template->trySet('title', $this->title);
            $this->template->trySet('headerCSS', $this->headerCSS);
        }

        if ($this->contentCSS) {
            $this->template->trySet('contentCSS', implode(' ', $this->contentCSS));
        }

        if (!empty($this->fx)) {
            $data['uri'] = $this->cb->getJSURL();
        }

        if (!$this->showActions) {
            $this->template->del('ActionContainer');
        }

        // call modal creation first
        if (isset($this->options['modal_option'])) {
            $this->js(true)->modal($this->options['modal_option']);
        } else {
            $this->js(true)->modal();
        }

        //add setting if available.
        if (isset($this->options['setting'])) {
            foreach ($this->options['setting'] as $key => $value) {
                $this->js(true)->modal('setting', $key, $value);
            }
        }

        if (!isset($this->options['modal_option']['closable']) || $this->options['modal_option']['closable']) {
            $this->template->trySet('close', 'icon close');
        }

        if (!empty($this->args)) {
            $data['args'] = $this->args;
        }
        $this->js(true)->data($data);

        parent::renderView();
    }

    public function renderMe()
    {
        $html = null;
        if (!$this->hasBeenRendered) {
            $html = $this->getHTML();
            $this->hasBeenRendered = true;
        }

        return $html;
    }
}
