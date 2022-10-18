<?php

declare(strict_types=1);

namespace Atk4\Ui\Panel;

use Atk4\Core\Factory;
use Atk4\Ui\Button;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsChain;
use Atk4\Ui\JsExpression;
use Atk4\Ui\Modal;
use Atk4\Ui\View;

/**
 * Right Panel implementation.
 * Opening, closing and loading Panel content is manage
 * via the js panel service.
 *
 * Content is loaded via a LoadableContent View.
 * This view must implement a callback for content to be added via the callback function.
 */
class Right extends View implements Loadable
{
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

    /** @var string The css selector on where to add close panel event triggering for closing it. */
    public $closeSelector = '.atk-panel-close';

    /** @var string a css selector where warning trigger class will be applied. */
    public $warningSelector = '.atk-panel-warning';

    /** @var string the css class name to apply to element set by warning selector. */
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

        $this->getApp()->registerPortals($this);
    }

    /**
     * Set the dynamic content of this view.
     */
    public function addDynamicContent(LoadableContent $content): void
    {
        $this->dynamicContent = Content::addTo($this, [], ['LoadContent']);
    }

    /**
     * Get dynamic content for this view.
     */
    public function getDynamicContent(): LoadableContent
    {
        return $this->dynamicContent;
    }

    /**
     * Return js expression in order to retrieve panelService.
     */
    public function service(): JsChain
    {
        return new JsChain('atk.panelService');
    }

    /**
     * Return js expression need to open panel via js panelService.
     *
     * @param array        $urlArgs       the argument to include when dynamic content panel open
     * @param array        $dataAttribute the data attribute name to include in reload from the triggering element
     * @param string|null  $activeCss     the css class name to apply on triggering element when panel is open
     * @param JsExpression $jsTrigger     JsExpression that trigger panel to open. Default = $(this).
     */
    public function jsOpen(array $urlArgs = [], array $dataAttribute = [], string $activeCss = null, JsExpression $jsTrigger = null): JsExpression
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
     * Will reload panel passing args as Get param via js flyoutService.
     */
    public function jsPanelReload(array $args = []): JsExpression
    {
        return $this->service()->reloadPanel($this->name, $args);
    }

    /**
     * Return js expression need to close panel via js panelService.
     */
    public function jsClose(): JsExpression
    {
        return $this->service()->closePanel($this->name);
    }

    /**
     * Attach confirmation modal view to display.
     * js flyoutService will prevent closing of Flyout if a confirmation modal
     * is attached to it and flyoutService detect that the current open flyoutContent has warning on.
     */
    public function addConfirmation(string $msg, string $title = 'Closing panel!', string $okBtn = null, string $cancelBtn = null): void
    {
        if (!$okBtn) {
            $okBtn = (new Button(['Ok']))->addClass('ok');
        }

        if (!$cancelBtn) {
            $cancelBtn = (new Button(['Cancel']))->addClass('cancel');
        }
        $this->closeModal = $this->getApp()->add(array_merge($this->defaultModal, ['title' => $title]));
        $this->closeModal->add([View::class, $msg, 'element' => 'p']);
        $this->closeModal->addButtonAction(Factory::factory($okBtn));
        $this->closeModal->addButtonAction(Factory::factory($cancelBtn));

        $this->closeModal->notClosable();
    }

    /**
     * Callback to execute when panel open if dynamic content is set.
     * Differ the callback execution to the FlyoutContent.
     */
    public function onOpen(\Closure $callback): void
    {
        $this->getDynamicContent()->onLoad($callback);
    }

    /**
     * Display or not a Warning sign in Panel.
     *
     * @return Jquery
     */
    public function jsDisplayWarning(bool $state = true): JsExpression
    {
        $chain = new Jquery('#' . $this->name . ' ' . $this->warningSelector);

        return $state ? $chain->addClass($this->warningTrigger) : $chain->removeClass($this->warningTrigger);
    }

    /**
     * Toggle warning sign.
     *
     * @return Jquery
     */
    public function jsToggleWarning(): JsExpression
    {
        return (new Jquery('#' . $this->name . ' ' . $this->warningSelector))->toggleClass($this->warningTrigger);
    }

    /**
     * Return panel options.
     */
    public function getPanelOptions(): array
    {
        $panel_options = [
            'id' => $this->name,
            'loader' => ['selector' => '.ui.loader', 'trigger' => 'active'], // the css selector and trigger class to activate loader.
            'modal' => $this->closeModal ? '#' . $this->closeModal->name : null,
            'warning' => ['selector' => $this->warningSelector, 'trigger' => $this->warningTrigger],
            'visible' => 'atk-visible', // the triggering css class that will make this panel visible.
            'closeSelector' => $this->closeSelector, // the css selector to close this flyout.
            'hasClickAway' => $this->hasClickAway,
            'hasEscAway' => $this->hasEscAway,
        ];

        if ($this->dynamicContent) {
            $panel_options['url'] = $this->getDynamicContent()->getCallbackUrl();
            $panel_options['clearable'] = $this->getDynamicContent()->getClearSelector();
        }

        return $panel_options;
    }

    protected function renderView(): void
    {
        $this->template->trySet('WarningIcon', $this->warningIcon);
        $this->template->trySet('CloseIcon', $this->closeIcon);

        parent::renderView();

        $this->js(true, $this->service()->addPanel($this->getPanelOptions()));
    }
}
