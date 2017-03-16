<?php

namespace atk4\ui;

/**
 * Virtual page normally does not render, yet it has it's own trigger and will respond 
 * to the trigger in a number of useful way depending on trigger's argument:
 *
 *  - cut = will only output HTML of this VirtualPage and it's sub-elements
 *  - popup = will add VirtualPage directly into body, ideal for pop-up windows
 *  - normal = will get rid of all the normal components inside Layout's content replacing them
 *      the render of this page. Will preserve menus and top bar but that's it.
 */
class VirtualPage extends View
{
    public $cb = null;

    public function init() {
        parent::init();

        $this->cb = $this->add('CallbackLater');

        $this->cb->set(function() {

            if ($this->cb->triggered == 'cut') {
                $this->app->terminate($this->render());
            }

            if ($this->cb->triggered == 'popup') {
                $this->ui = 'container'; // to maintain some gaps.. 
                $this->app->html->template->set('title', $this->app->title);
                $this->app->html->template->setHTML('Content', parent::getHTML());
                $this->app->html->template->appendHTML('HEAD', $this->getJS());

                $this->app->terminate($this->app->html->template->render());
            }

            // Remove all elements from inside the Content
            foreach($this->app->layout->elements as $key => $view) {
                if ($view instanceof View && $view->region == 'Content') {
                    unset($this->app->layout->elements[$key]);
                }
            }

            $this->app->layout->template->setHTML('Content', parent::getHTML());
            $this->app->layout->_js_actions = array_merge($this->app->layout->_js_actions, $this->_js_actions);

            $this->app->html->template->setHTML('Content', $this->app->layout->getHTML());
            $this->app->html->template->appendHTML('HEAD', $this->app->layout->getJS());

            $this->app->terminate($this->app->html->template->render());
        });
    }

    public function getURL($mode = 'callback') {
        return $this->cb->getURL($mode);
    }

    /**
     * VirtualPage is not rendered normally. It's invisible. Only when
     * it is triggered, it will exclusively output
     * it's content
     */
    public function getHTML() {
    }
}
