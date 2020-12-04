<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Virtual page normally does not render, yet it has it's own trigger and will respond
 * to the trigger in a number of useful way depending on trigger's argument:.
 *
 *  - cut = will only output HTML of this VirtualPage and it's sub-elements
 *  - popup = will add VirtualPage directly into body, ideal for pop-up windows
 *  - normal = will get rid of all the normal components inside Layout's content replacing them
 *      the render of this page. Will preserve menus and top bar but that's it.
 */
class VirtualPage extends View
{
    /** @var Callback */
    public $cb;

    /** @var \Closure Optional callback function of virtual page */
    public $fx;

    /** @var string specify custom callback trigger for the URL (see Callback::$urlTrigger) */
    public $urlTrigger;

    /** @var string UI container class */
    public $ui = 'container';

    /**
     * Initialization.
     */
    protected function init(): void
    {
        parent::init();

        $this->cb = $this->add([Callback::class, 'urlTrigger' => $this->urlTrigger ?: $this->name]);
    }

    /**
     * Set callback function of virtual page.
     *
     * Note that only one callback function can be defined.
     *
     * @param array $fx   Need this to be defined as array otherwise we get warning in PHP7
     * @param mixed $junk
     *
     * @return $this
     */
    public function set($fx = [], $junk = null)
    {
        if (!$fx) {
            return $this;
        }

        if ($this->fx) {
            throw (new Exception('Callback for this Virtual Page is already defined'))
                ->addMoreInfo('vp', $this)
                ->addMoreInfo('old_fx', $this->fx)
                ->addMoreInfo('new_fx', $fx);
        }
        $this->fx = $fx;

        return $this;
    }

    /**
     * Is virtual page active?
     */
    public function isTriggered(): bool
    {
        return $this->cb->isTriggered();
    }

    /**
     * Returns URL which you can load directly in the browser location, open in a new tab,
     * new window or inside iframe. This URL will contain HTML for a new page.
     *
     * @param string $mode
     *
     * @return string
     */
    public function getUrl($mode = 'callback')
    {
        return $this->cb->getUrl($mode);
    }

    /**
     * Return URL that is designed to be loaded from inside JavaScript and contain JSON code.
     * This is useful for dynamically loaded Modal, Tab or Loader.
     *
     * @param string $mode
     *
     * @return string
     */
    public function getJsUrl($mode = 'callback')
    {
        return $this->cb->getJsUrl($mode);
    }

    /**
     * VirtualPage is not rendered normally. It's invisible. Only when
     * it is triggered, it will exclusively output it's content.
     */
    public function getHtml()
    {
        $this->cb->set(function () {
            // if virtual page callback is triggered
            if ($mode = $this->cb->getTriggeredValue()) {
                // process callback
                if ($this->fx) {
                    ($this->fx)($this);
                }

                // special treatment for popup
                if ($mode === 'popup') {
                    $this->app->html->template->set('title', $this->app->title);
                    $this->app->html->template->setHtml('Content', parent::getHtml());
                    $this->app->html->template->appendHtml('HEAD', $this->getJs());

                    $this->app->terminateHtml($this->app->html->template);
                }

                // render and terminate
                if (isset($_GET['__atk_json'])) {
                    $this->app->terminateJson($this);
                }

                if (isset($_GET['__atk_tab'])) {
                    $this->app->terminateHtml($this->renderToTab());
                }

                // do not terminate if callback supplied (no cutting)
                if ($mode !== 'callback') {
                    $this->app->terminateHtml($this);
                }
            }

            // Remove all elements from inside the Content
            foreach ($this->app->layout->elements as $key => $view) {
                if ($view instanceof View && $view->region === 'Content') {
                    unset($this->app->layout->elements[$key]);
                }
            }

            // Prepare modals in order to include them in VirtualPage.
            $modalHtml = '';
            foreach ($this->app->html !== null ? $this->app->html->elements : [] as $view) {
                if ($view instanceof Modal) {
                    $modalHtml .= $view->getHtml();
                    $this->app->layout->_js_actions = array_merge($this->app->layout->_js_actions, $view->_js_actions);
                }
            }

            $this->app->layout->template->setHtml('Content', parent::getHtml());
            $this->app->layout->_js_actions = array_merge($this->app->layout->_js_actions, $this->_js_actions);

            $this->app->html->template->setHtml('Content', $this->app->layout->template->render());
            $this->app->html->template->setHtml('Modals', $modalHtml);

            $this->app->html->template->appendHtml('HEAD', $this->app->layout->getJs());

            $this->app->terminateHtml($this->app->html->template);
        });
    }

    protected function mergeStickyArgsFromChildView(): ?AbstractView
    {
        return $this->cb;
    }
}
