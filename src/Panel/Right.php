<?php

declare(strict_types=1);

namespace Atk4\Ui\Panel;

use Atk4\Core\Factory;
use Atk4\Ui\Button;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Modal;
use Atk4\Ui\View;

/**
 * Right Panel implementation.
 * Opening, closing and loading Panel content is manage
 * via the JS panel service.
 *
 * Content is loaded via a LoadableContent View.
 * This view must implement a callback for content to be added via the callback function.
 */
class Right extends View implements Loadable
{
    public array $class = ['atk-right-panel'];
    public $defaultTemplate = 'panel/right.html';

    /** @var Modal|null */
    public $closeModal;
    /** @var array Confirmation Modal default */
    public $defaultModal = [Modal::class, 'class' => ['mini']];

    /** @var View|null The content to display inside flyout */
    protected $dynamicContent;

    /** @var bool can be closed by clicking outside panel. */
    protected $hasClickAway = true;

    /** @var bool can be closed via esc key. */
    protected $hasEscAway = true;

    /** @var array The default content seed. */
    public $dynamic = [Content::class];

    /** @var string The CSS selector on where to add close panel event triggering for closing it. */
    public $closeSelector = '.atk-panel-close';

    /** @var string a CSS selector where warning trigger class will be applied. */
    public $warningSelector = '.atk-panel-warning';

    /** @var string the CSS class name to apply to element set by warning selector. */
    public $warningTrigger = 'atk-visible';

    /** @var string the warning icon class */
    public $warningIcon = 'exclamation circle';

    /** @var string the close icon class */
    public $closeIcon = 'times';

    protected function init(): void
    {
        parent::init();

        if ($this->dynamic) {
            $this->addDynamicContent(Factory::factory($this->dynamic));
        }
    }

    /**
     * Set the dynamic content of this view.
     */
    public function addDynamicContent(LoadableContent $content): void
    {
        $this->dynamicContent = Content::addTo($this, [], ['LoadContent']);
    }

    public function getDynamicContent(): LoadableContent
    {
        return $this->dynamicContent;
    }

    /**
     * Return JS expression in order to retrieve panelService.
     */
    public function service(): JsChain
    {
        return new JsChain('atk.panelService');
    }

    /**
     * Return JS expression need to open panel via JS panelService.
     *
     * @param array<string, string> $urlArgs       the argument to include when dynamic content panel open
     * @param array                 $dataAttribute the data attribute name to include in reload from the triggering element
     * @param string|null           $activeCss     the CSS class name to apply on triggering element when panel is open
     * @param JsExpressionable      $jsTrigger     JS expression that trigger panel to open. Default = $(this).
     */
    public function jsOpen(array $urlArgs = [], array $dataAttribute = [], string $activeCss = null, JsExpressionable $jsTrigger = null): JsExpressionable
    {
        return $this->service()->openPanel([
            'triggered' => $jsTrigger ?? new Jquery(),
            'reloadArgs' => $dataAttribute,
            'urlArgs' => $urlArgs,
            'openId' => $this->name,
            'activeCSS' => $activeCss,
        ]);
    }

    /**
     * Will reload panel passing args as Get param via JS flyoutService.
     */
    public function jsPanelReload(array $args = []): JsExpressionable
    {
        return $this->service()->reloadPanel($this->name, $args);
    }

    /**
     * Return JS expression need to close panel via JS panelService.
     */
    public function jsClose(): JsExpressionable
    {
        return $this->service()->closePanel($this->name);
    }

    /**
     * Attach confirmation modal view to display.
     * JS flyoutService will prevent closing of Flyout if a confirmation modal
     * is attached to it and flyoutService detect that the current open flyoutContent has warning on.
     */
    public function addConfirmation(string $msg, string $title = 'Closing panel!', string $okButton = null, string $cancelButton = null): void
    {
        if (!$okButton) {
            $okButton = (new Button(['Ok']))->addClass('ok');
        }

        if (!$cancelButton) {
            $cancelButton = (new Button(['Cancel']))->addClass('cancel');
        }
        $this->closeModal = $this->getApp()->add(array_merge($this->defaultModal, ['title' => $title]));
        $this->closeModal->add([View::class, $msg, 'element' => 'p']);
        $this->closeModal->addButtonAction(Factory::factory($okButton));
        $this->closeModal->addButtonAction(Factory::factory($cancelButton));

        $this->closeModal->notClosable();
    }

    /**
     * Callback to execute when panel open if dynamic content is set.
     * Differ the callback execution to the FlyoutContent.
     *
     * @param \Closure(object): void $fx
     */
    public function onOpen(\Closure $fx): void
    {
        $this->getDynamicContent()->onLoad($fx);
    }

    /**
     * Display or not a Warning sign in Panel.
     *
     * @return Jquery
     */
    public function jsDisplayWarning(bool $state = true): JsExpressionable
    {
        $chain = new Jquery('#' . $this->name . ' ' . $this->warningSelector);

        return $state ? $chain->addClass($this->warningTrigger) : $chain->removeClass($this->warningTrigger);
    }

    /**
     * Toggle warning sign.
     *
     * @return Jquery
     */
    public function jsToggleWarning(): JsExpressionable
    {
        return (new Jquery('#' . $this->name . ' ' . $this->warningSelector))->toggleClass($this->warningTrigger);
    }

    public function getPanelOptions(): array
    {
        $res = [
            'id' => $this->name,
            'loader' => ['selector' => '.ui.loader', 'trigger' => 'active'], // the CSS selector and trigger class to activate loader
            'modal' => $this->closeModal,
            'warning' => ['selector' => $this->warningSelector, 'trigger' => $this->warningTrigger],
            'visible' => 'atk-visible', // the triggering CSS class that will make this panel visible
            'closeSelector' => $this->closeSelector, // the CSS selector to close this flyout
            'hasClickAway' => $this->hasClickAway,
            'hasEscAway' => $this->hasEscAway,
        ];

        if ($this->dynamicContent) {
            $res['url'] = $this->getDynamicContent()->getCallbackUrl();
            $res['clearable'] = $this->getDynamicContent()->getClearSelector();
        }

        return $res;
    }

    protected function renderView(): void
    {
        $this->template->trySet('WarningIcon', $this->warningIcon);
        $this->template->trySet('CloseIcon', $this->closeIcon);

        parent::renderView();

        $this->js(true, $this->service()->addPanel($this->getPanelOptions()));
    }
}
