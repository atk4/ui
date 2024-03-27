<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsExpressionable;

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
    public $headerClass = 'header';
    /** @var \Closure(View): void|null */
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

    /** @var array Add ability to add CSS classes to "content" div. */
    public $contentClass = ['img', 'content', 'atk-dialog-content'];

    /**
     * If true, the <div class="actions"> at the bottom of the modal is
     * shown. Automatically set to true if any actions are added.
     *
     * @var bool
     */
    public $showActions = false;

    /**
     * Set callback function for this modal.
     *
     * @param \Closure(View): void $fx
     */
    #[\Override]
    public function set($fx = null)
    {
        if (!$fx instanceof \Closure) {
            throw new \TypeError('$fx must be of type Closure');
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

            $this->cb->terminateJsonIfCanTerminate($this->cbView);
        });
    }

    /**
     * Add CSS classes to "content" div.
     *
     * @param string|array $class
     */
    public function addContentClass($class): void
    {
        $this->contentClass = array_merge($this->contentClass, is_string($class) ? [$class] : $class);
    }

    /**
     * Show modal on page.
     *
     * Example: $button->on('click', $modal->jsShow());
     *
     * @return JsChain
     */
    public function jsShow(array $args = []): JsExpressionable
    {
        $chain = $this->js();
        if ($args !== []) {
            $chain->data(['args' => $args]);
        }

        return $chain->modal('show');
    }

    /**
     * Hide modal from page.
     *
     * @return JsChain
     */
    public function jsHide(): JsExpressionable
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
        $this->addContentClass('scrolling');

        return $this;
    }

    /**
     * Add a deny action to modal.
     *
     * @param string           $label
     * @param JsExpressionable $jsAction will run when deny is click
     *
     * @return $this
     */
    public function addDenyAction($label, JsExpressionable $jsAction)
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
     * @param JsExpressionable $jsAction will run when deny is click
     *
     * @return $this
     */
    public function addApproveAction($label, JsExpressionable $jsAction)
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

    #[\Override]
    protected function renderView(): void
    {
        $data = [];
        $data['type'] = $this->type;
        $data['loadingLabel'] = $this->loadingLabel;

        if ($this->title) {
            $this->template->trySet('title', $this->title);
            $this->template->trySet('headerClass', $this->headerClass);
        } else {
            // fix top modal corner rounding, first div must not be empty (must not be lower than 5px)
            // https://github.com/fomantic/Fomantic-UI/blob/2.9.0/src/definitions/modules/modal.less#L43
            $this->template->loadFromString(preg_replace('~<div class="\{\$headerClass\}">\{\$title\}</div>\s*~', '', $this->template->toLoadableString(), 1));
        }

        $this->template->trySet('contentClass', implode(' ', $this->contentClass));

        if ($this->fx !== null) {
            $data['url'] = $this->cb->getJsUrl();
        }

        if (!$this->showActions) {
            $this->template->del('ActionContainer');
        }

        $this->js(true)->modal($this->options);

        if (!isset($this->options['closable']) || $this->options['closable']) {
            $this->template->trySet('closeIcon', 'close');
        } else {
            // fix no extra space for icon
            // TODO should be replaced with i tag render
            $this->template->loadFromString(preg_replace('~<i class="\{\$closeIcon\} icon"></i>~', '', $this->template->toLoadableString(), 1));
        }

        if ($this->args) {
            $data['args'] = $this->args;
        }
        $this->js(true)->data($data);

        parent::renderView();
    }
}
