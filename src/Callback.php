<?php

declare(strict_types=1);

namespace atk4\ui;

use atk4\core\AppScopeTrait;
use atk4\core\DiContainerTrait;
use atk4\core\InitializerTrait;
use atk4\core\StaticAddToTrait;
use atk4\core\TrackableTrait;

/**
 * Add this object to your render tree and it will expose a unique URL which, when
 * executed directly will perform a PHP callback that you set().
 *
 * $button = Button::addTo($layout);
 * $button->set('Click to do something')->link(
 *      Callback::addTo($button)
 *          ->set(function(){
 *              do_something();
 *          })
 *          ->getUrl()
 *  );
 *
 * @property View $owner
 */
class Callback
{
    use TrackableTrait;
    use AppScopeTrait;
    use DiContainerTrait;
    use InitializerTrait {
        init as _init;
    }
    use StaticAddToTrait;

    /**
     * Whether urlTrigger is set via Post or Get (default).
     *
     * @var bool
     */
    public $isPostTriggered;

    /**
     * Specify a custom GET or POST trigger here.
     *
     * @var string|null
     */
    private $urlTrigger;

    /**
     * Initialize object and set default properties.
     *
     * @param array|string $defaults
     */
    public function __construct($defaults = [])
    {
        $this->setDefaults($defaults);
    }

    /**
     * Initialization.
     */
    public function init(): void
    {
        $this->_init();

        if (!$this->app) {
            throw new Exception('Call-back must be part of a RenderTree');
        }

        if (!$this->urlTrigger) {
            $this->urlTrigger = $this->name;
        }

        $this->setAppSticky();
    }

    public function setUrlTrigger(string $trigger)
    {
        $this->urlTrigger = $trigger;
        $this->setAppSticky();
    }

    /**
     * Set app sticky argument only when using GET method.
     */
    public function setAppSticky()
    {
        if (!$this->isPostTriggered) {
            $this->app->stickyGet($this->urlTrigger);
        }
    }

    public function getUrlTrigger(): string
    {
        return $this->urlTrigger;
    }

    /**
     * Executes user-specified action when call-back is triggered.
     *
     * @param callable $callback
     * @param array    $args
     *
     * @return mixed|null
     */
    public function set($callback, $args = [])
    {
        if ($this->isTriggered()) {
            $this->app->catch_runaway_callbacks = false;
            $t = $this->app->run_called;
            $this->app->run_called = true;
            $ret = call_user_func_array($callback, $args);
            $this->app->run_called = $t;

            return $ret;
        }
    }

    /**
     * Terminate this callback
     * by rendering the owner view by default.
     */
    public function terminateJson(View $view = null)
    {
        if ($this->canTerminate()) {
            $this->app->terminateJson($view ?: $this->owner);
        }
    }

    /**
     * Prevent callback from terminating during a reload.
     */
    protected function canTerminate(): bool
    {
        $reload = $_GET['__atk_reload'] ?? null;

        return !$reload || $this->owner->name === $reload;
    }

    public function isTriggered(): bool
    {
        if ($this->isPostTriggered) {
            return isset($_POST[$this->urlTrigger]);
        }

        return isset($_GET[$this->urlTrigger]);
    }

    /**
     * Return callback triggered value.
     */
    public function getTriggeredValue(): string
    {
        if ($this->isPostTriggered) {
            return  $_POST[$this->urlTrigger] ?? '';
        }

        return $_GET[$this->urlTrigger] ?? '';
    }

    /**
     * Return URL that will trigger action on this call-back. If you intend to request
     * the URL direcly in your browser (as iframe, new tab, or document location), you
     * should use getUrl instead.
     */
    public function getJsUrl(string $value = 'ajax'): string
    {
        return $this->owner->jsUrl($this->getUrlArguments($value));
    }

    /**
     * Return URL that will trigger action on this call-back. If you intend to request
     * the URL loading from inside JavaScript, it's always advised to use getJsUrl instead.
     *
     */
    public function getUrl(string $value = 'callback'): string
    {
        return $this->owner->url($this->getUrlArguments($value));
    }

    /**
     * Return proper url argument for this callback.
     */
    private function getUrlArguments(string $value): array
    {
        $args = ['__atk_callback' => 1];
        if (!$this->isPostTriggered) {
            $args[$this->urlTrigger] = $value;
        }

        return $args;
    }
}
