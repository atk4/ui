<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * This class add modal dialog to a page.
 *
 * Modal are added to the layout but their content is hidden by default.
 * $modal->jsShow() is the triggered needed to actually display the modal.
 *
 * Modal can be use as a regular view, simply by adding other view to it.
 *  Message::addTo($modal, ['title' => 'Welcome to Agile Toolkit'])->text('Your text here');
 *
 * Modal can add content dynamically via CallbackLater.
 *  $modal->set(function (View $p) {
 *     Form::addTo($p);
 *  });
 *
 * Modal can use Fomantic-UI predefine method onApprove or onDeny by passing
 * a jsAction to Modal::addDenyAction or Modal::addApproveAction method. It will not close until the jsAction return true.
 *  $modal->addDenyAction('No', new JsExpression('function () { window.alert(\'Cannot do that.\'); return false; }'));
 *  $modal->addApproveAction('Yes', new JsExpression('function () { window.alert(\'You are good to go!\'); }'));
 *
 * You may also prevent modal from closing via the esc or dimmed area click using $modal->notClosable().
 */
class Modal extends View
{
    public $ui = 'modal';
    public $defaultTemplate = 'modal.html';

    /** @var string|null Set null for no title */
    public $title;
    /** @var string */
    public $loadingLabel = 'Loading...';
    /** @var string */
    public $headerCss = 'header';
    /** @var \Closure|null */
    public $fx;
    /** @var CallbackLater|null */
    public $cb;
    /** @var View|null */
    public $cbView;
    /** @var array */
    public $args = [];
    /** @var array */
    public $options = [];

    /** @var string Currently only "json" response type is supported. */
    public $type = 'json';

    /** @var array Add ability to add css classes to "content" div. */
    public $contentCss = ['img', 'content', 'atk-dialog-content'];

    /**
     * If true, the <div class="actions"> at the bottom of the modal is
     * shown. Automatically set to true if any actions are added.
     *
     * @var bool
     */
    public $showActions = false;

    protected function init(): void
    {
        parent::init();

        $this->getApp()->registerPortals($this);
    }

    /**
     * Set callback function for this modal.
     * $fx is set as an array in order to comply with View::set().
     * TODO Rename this function and break BC?
     *
     * @param \Closure $fx
     *
     * @return $this
     */
    public function set($fx = null, $ignore = null)
    {
        if (!$fx instanceof \Closure) {
            throw new Exception('Need to pass a function to Modal::set()');
        } elseif (func_num_args() > 1) {
            throw new Exception('Only one argument is needed by Modal::set()');
        }

        $this->fx = $fx;
        $this->enableCallback();

        return $this;
    }

    /**
     * Add View to be loaded in this modal and
     * attach CallbackLater to it.
     * The cbView only will be loaded dynamically within modal
     * div.atk-content.
     */
    public function enableCallback(): void
    {
        $this->cbView = View::addTo($this);
        $this->cbView->stickyGet('__atk_m', $this->name);
        if (!$this->cb) {
            $this->cb = CallbackLater::addTo($this->cbView);
        }

        $this->cb->set(function () {
            ($this->fx)($this->cbView);
            $this->cb->terminateJson($this->cbView);
        });
    }

    /**
     * Add CSS classes to "content" div.
     *
     * @param string|array $class
     */
    public function addContentCss($class): void
    {
        $this->contentCss = array_merge($this->contentCss, is_string($class) ? [$class] : $class);
    }

    /**
     * Show modal on page.
     *
     * Example: $button->on('click', $modal->jsShow());
     *
     * @return JsChain
     */
    public function jsShow(array $args = [])
    {
        $js_chain = $this->js();
        if ($args !== []) {
            $js_chain->data(['args' => $args]);
        }

        return $js_chain->modal('show');
    }

    /**
     * Hide modal from page.
     *
     * @return JsChain
     */
    public function jsHide()
    {
        return $this->js()->modal('hide');
    }

    /**
     * Set modal option.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Add scrolling capability to modal.
     *
     * @return $this
     */
    public function addScrolling()
    {
        $this->addContentCss('scrolling');

        return $this;
    }

    /**
     * Add a deny action to modal.
     *
     * @param string           $label
     * @param JsExpressionable $jsAction javascript action that will run when deny is click
     *
     * @return $this
     */
    public function addDenyAction($label, $jsAction)
    {
        $button = new Button();
        $button->set($label)->addClass('red cancel');
        $this->addButtonAction($button);
        $this->options['onDeny'] = $jsAction;

        return $this;
    }

    /**
     * Add an approve action button to modal.
     *
     * @param string           $label
     * @param JsExpressionable $jsAction javascript action that will run when deny is click
     *
     * @return $this
     */
    public function addApproveAction($label, $jsAction)
    {
        $b = new Button();
        $b->set($label)->addClass('green ok');
        $this->addButtonAction($b);
        $this->options['onApprove'] = $jsAction;

        return $this;
    }

    /**
     * Add an action button to modal.
     *
     * @param View $button
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
        $this->options['closable'] = false;

        return $this;
    }

    protected function renderView(): void
    {
        $data = [];
        $data['type'] = $this->type;
        $data['loadingLabel'] = $this->loadingLabel;

        if ($this->title) {
            $this->template->trySet('title', $this->title);
            $this->template->trySet('headerCss', $this->headerCss);
        }

        if ($this->contentCss) {
            $this->template->trySet('contentCss', implode(' ', $this->contentCss));
        }

        if ($this->fx !== null) {
            $data['url'] = $this->cb->getJsUrl();
        }

        if (!$this->showActions) {
            $this->template->del('ActionContainer');
        }

        $this->js(true)->modal($this->options);

        if (!isset($this->options['closable']) || $this->options['closable']) {
            $this->template->trySet('closeIcon', 'close');
        }

        if ($this->args) {
            $data['args'] = $this->args;
        }
        $this->js(true)->data($data);

        parent::renderView();
    }
}
