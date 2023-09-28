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
 *          ->set(function () {
 *              do_something();
 *          })
 *          ->getUrl()
 *  );
 */
class Callback extends AbstractView
{
    public const URL_QUERY_TRIGGER_PREFIX = '__atk_cb_';
    public const URL_QUERY_TARGET = '__atk_cbtarget';

    /** @var string Specify a custom GET trigger. */
    protected $urlTrigger;

    /** @var bool Allow this callback to trigger during a reload. */
    public $triggerOnReload = true;

    public function add(AbstractView $object, array $args = []): AbstractView
    {
        throw new Exception('Callback cannot contain children');
    }

    protected function init(): void
    {
        $this->getApp(); // assert has App

        parent::init();

        $this->setUrlTrigger($this->urlTrigger);
    }

    public function setUrlTrigger(string $trigger = null): void
    {
        $this->urlTrigger = $trigger ?? $this->name;

        $this->getOwner()->stickyGet(self::URL_QUERY_TRIGGER_PREFIX . $this->urlTrigger);
    }

    public function getUrlTrigger(): string
    {
        return $this->urlTrigger;
    }

    /**
     * Executes user-specified action when callback is triggered.
     *
     * @template T
     *
     * @param \Closure(mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed, mixed): T $fx
     * @param array                                                                             $fxArgs
     *
     * @return T|null
     */
    public function set($fx = null, $fxArgs = null)
    {
        if ($this->isTriggered() && $this->canTrigger()) {
            try {
                return $fx(...($fxArgs ?? []));
            } catch (\Exception $e) {
                // catch and wrap an exception using a custom Error class to prevent "Callback requested, but never reached"
                // exception which is hard to understand/locate as thrown from the main app context
                throw new UnhandledCallbackExceptionError('', 0, $e);
            }
        }

        return null;
    }

    /**
     * Terminate this callback by rendering the given view.
     */
    public function terminateJson(View $view): void
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
        return $this->getApp()->hasRequestQueryParam(self::URL_QUERY_TRIGGER_PREFIX . $this->urlTrigger);
    }

    public function getTriggeredValue(): string
    {
        return $this->getApp()->tryGetRequestQueryParam(self::URL_QUERY_TRIGGER_PREFIX . $this->urlTrigger) ?? '';
    }

    /**
     * Only current callback can terminate.
     */
    public function canTerminate(): bool
    {
        return $this->getApp()->hasRequestQueryParam(self::URL_QUERY_TARGET) && $this->getApp()->getRequestQueryParam(self::URL_QUERY_TARGET) === $this->urlTrigger;
    }

    /**
     * Allow callback to be triggered or not.
     */
    public function canTrigger(): bool
    {
        return $this->triggerOnReload || !$this->getApp()->hasRequestQueryParam('__atk_reload');
    }

    /**
     * Return URL that will trigger action on this callback. If you intend to request
     * the URL directly in your browser (as iframe, new tab, or document location), you
     * should use getUrl instead.
     */
    public function getJsUrl(string $value = 'ajax'): string
    {
        return $this->getOwner()->jsUrl($this->getUrlArguments($value));
    }

    /**
     * Return URL that will trigger action on this callback. If you intend to request
     * the URL loading from inside JavaScript, it's always advised to use getJsUrl instead.
     */
    public function getUrl(string $value = 'callback'): string
    {
        return $this->getOwner()->url($this->getUrlArguments($value));
    }

    /**
     * Return proper URL argument for this callback.
     */
    private function getUrlArguments(string $value = null): array
    {
        return [
            self::URL_QUERY_TARGET => $this->urlTrigger,
            self::URL_QUERY_TRIGGER_PREFIX . $this->urlTrigger => $value ?? ($this->isTriggered() ? $this->getTriggeredValue() : ''),
        ];
    }
}
