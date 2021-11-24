<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\Exception\UnhandledCallbackExceptionError;

/**
 * Add this object to your render tree and it will expose a unique URL which, when
 * executed directly will perform a PHP callback that you set().
 *
 * Callback function run when triggered, i.e. when it's urlTrigger param value is present in the $_GET request.
 * The current callback will be set within the $_GET[Callback::URL_QUERY_TARGET] and will be set to urlTrigger as well.
 *
 * $button = Button::addTo($layout);
 * $button->set('Click to do something')->link(
 *      Callback::addTo($button)
 *          ->set(function(){
 *              do_something();
 *          })
 *          ->getUrl()
 *  );
 */
class Callback extends AbstractView
{
    /** @const string */
    public const URL_QUERY_TRIGGER_PREFIX = '__atk_cb_';

    /** @const string */
    public const URL_QUERY_TARGET = '__atk_cbtarget';

    /** @var string Specify a custom GET trigger. */
    protected $urlTrigger;

    /** @var bool Allow this callback to trigger during a reload. */
    public $triggerOnReload = true;

    public function add($object, $args = null): AbstractView
    {
        throw new Exception('Callback cannot contains children');
    }

    /**
     * Initialization.
     */
    protected function init(): void
    {
        $this->getApp(); // assert has App

        parent::init();

        $this->setUrlTrigger($this->urlTrigger);
    }

    public function setUrlTrigger(string $trigger = null)
    {
        $this->urlTrigger = $trigger ?: $this->name;

        $this->getOwner()->stickyGet(self::URL_QUERY_TRIGGER_PREFIX . $this->urlTrigger);
    }

    public function getUrlTrigger(): string
    {
        return $this->urlTrigger;
    }

    /**
     * Executes user-specified action when call-back is triggered.
     *
     * @param \Closure $fx
     * @param array    $args
     *
     * @return mixed
     */
    public function set($fx = null, $args = null)
    {
        if ($this->isTriggered() && $this->canTrigger()) {
            try {
                return $fx(...($args ?? []));
            } catch (\Exception $e) {
                // wrap exception using a custom Error class to prevent "Callback requested, but never reached"
                // exception which is hard to understand/locate as thrown from the main context
                throw new UnhandledCallbackExceptionError('', 0, $e);
            }
        }
    }

    /**
     * Terminate this callback by rendering the given view.
     */
    public function terminateJson(AbstractView $view): void
    {
        if ($this->canTerminate()) {
            $this->getApp()->terminateJson($view);
        }
    }

    /**
     * Return true if urlTrigger is part of the request.
     */
    public function isTriggered(): bool
    {
        return isset($_GET[self::URL_QUERY_TRIGGER_PREFIX . $this->urlTrigger]);
    }

    /**
     * Return callback triggered value.
     */
    public function getTriggeredValue(): string
    {
        return $_GET[self::URL_QUERY_TRIGGER_PREFIX . $this->urlTrigger] ?? '';
    }

    /**
     * Only current callback can terminate.
     */
    public function canTerminate(): bool
    {
        return isset($_GET[self::URL_QUERY_TARGET]) && $_GET[self::URL_QUERY_TARGET] === $this->urlTrigger;
    }

    /**
     * Allow callback to be triggered or not.
     */
    public function canTrigger(): bool
    {
        return $this->triggerOnReload || empty($_GET['__atk_reload']);
    }

    /**
     * Return URL that will trigger action on this call-back. If you intend to request
     * the URL directly in your browser (as iframe, new tab, or document location), you
     * should use getUrl instead.
     */
    public function getJsUrl(string $value = 'ajax'): string
    {
        return $this->getOwner()->jsUrl($this->getUrlArguments($value));
    }

    /**
     * Return URL that will trigger action on this call-back. If you intend to request
     * the URL loading from inside JavaScript, it's always advised to use getJsUrl instead.
     */
    public function getUrl(string $value = 'callback'): string
    {
        return $this->getOwner()->url($this->getUrlArguments($value));
    }

    /**
     * Return proper url argument for this callback.
     */
    private function getUrlArguments(string $value = null): array
    {
        return [self::URL_QUERY_TARGET => $this->urlTrigger, self::URL_QUERY_TRIGGER_PREFIX . $this->urlTrigger => $value ?? $this->getTriggeredValue()];
    }
}
