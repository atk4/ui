<?php
/**
 * Right Panel implementation.
 * Opening, closing and loading Panel content is manage
 * via the js panel service.
 *
 * Content is loaded via a LoadableContent View.
 * This view must implement a callback for content to be add via the callback function.
 */

namespace atk4\ui\Panel;

use atk4\ui\Button;
use atk4\ui\jQuery;
use atk4\ui\jsExpression;
use atk4\ui\Modal;
use atk4\ui\View;

class Right extends View implements Loadable
{
    public $defaultTemplate = 'panel/right.html';

    /** @var null */
    public $closeModal;
    /** @var array Confirmation Modal default */
    public $defaultModal = [Modal::class, 'class' => ['mini']];

    /** @var View|null The content to display inside flyout */
    protected $dynamicContent;

    /** @var bool can be closed via esc or by clicking outside panel. */
    protected $hasClickAway = true;

    /** @var array The default content seed. */
    public $dynamic = [Content::class];

    /** @var string The css selector on where to add close panel event triggering for closing it. */
    public $closeSelector = '.atk-panel-close';

    /** @var string a css selector where warning trigger class will be applied. */
    public $warningSelector = '.atk-panel-warning';

    /** @var string the css class name to apply to element set by warning selector. */
    public $warningTrigger = 'atk-visible';

    /** @var string the warning icon class */
    public $warningIcon = 'icon exclamation circle';

    /** @var string the close icon class */
    public $closeIcon = 'icon times';

    public function init(): void
    {
        parent::init();
        if ($this->dynamic) {
            $this->addDynamicContent($this->factory($this->dynamic));
        }
    }

    /**
     * Set the dynamic content of this view.
     *
     * @throws \atk4\core\Exception
     */
    public function addDynamicContent(LoadableContent $content)
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
     *
     * @return mixed
     */
    public function service(): jsExpression
    {
        return new \atk4\ui\jsChain('atk.panelService');
    }

    /**
     * Return js expression need to open panel via js panelService.
     *
     * @param array        $args      The data attribute name to include in reload from the triggering element.
     * @param string|null  $activeCss The css class name to apply on triggering element when panel is open.
     * @param jsExpression $jsTrigger jsExpression that trigger panel to open. Default = $(this).
     *
     * @return mixed
     */
    public function jsOpen(array $args = [], string $activeCss = null, jsExpression $jsTrigger = null): jsExpression
    {
        return $this->service()->openPanel([
            'triggered' => $jsTrigger ?? new jQuery(),
            'reloadArgs' => $args,
            'openId' => $this->name,
            'activeCSS' => $activeCss,
        ]);
    }

    /**
     * Will reload panel passing args as Get param via js flyoutService.
     *
     * @return mixed
     */
    public function jsPanelReload(array $args = []): jsExpression
    {
        return $this->service()->reloadPanel($this->name, $args);
    }

    /**
     * Return js expression need to close panel via js panelService.
     *
     * @return mixed
     */
    public function jsClose(): jsExpression
    {
        return $this->service()->closePanel($this->name);
    }

    /**
     * Attach confirmation modal view to display.
     * js flyoutService will prevent closing of Flyout if a confirmation modal
     * is attached to it and flyoutService detect that the current open flyoutContent has warning on.
     *
     * @param $msg
     * @param null $okBtn
     * @param null $cancelBtn
     *
     * @throws \atk4\core\Exception
     * @throws \atk4\ui\Exception
     */
    public function addConfirmation(string $msg, string $title = 'Closing panel!', string $okBtn = null, string $cancelBtn = null)
    {
        if (!$okBtn) {
            $okBtn = (new Button(['Ok']))->addClass('ok');
        }

        if (!$cancelBtn) {
            $cancelBtn = (new Button(['Cancel']))->addClass('cancel');
        }
        $this->closeModal = $this->app->add(array_merge($this->defaultModal, ['title' => $title]));
        $this->closeModal->add([View::class, $msg, 'element' => 'p']);
        $this->closeModal->addButtonAction($this->factory($okBtn));
        $this->closeModal->addButtonAction($this->factory($cancelBtn));

        $this->closeModal->notClosable();
    }

    /**
     * Callback to execute when panel open if dynamic content is set.
     * Differ the callback execution to the FlyoutContent.
     */
    public function onOpen(callable $callback)
    {
        $this->getDynamicContent()->onLoad($callback);
    }

    /**
     * Display or not a Warning sign in Panel.
     *
     * @param string $selector
     *
     * @return jQuery
     */
    public function jsDisplayWarning(bool $state = true): jsExpression
    {
        $chain = new jQuery('#' . $this->name . ' ' . $this->warningSelector);

        return $state ? $chain->addClass($this->warningTrigger) : $chain->removeClass($this->warningTrigger);
    }

    /**
     * Toggle warning sign.
     *
     * @return jQuery
     */
    public function jsToggleWarning(): jsExpression
    {
        return (new jQuery('#' . $this->name . ' ' . $this->warningSelector))->toggleClass($this->warningTrigger);
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
        ];

        if ($this->dynamicContent) {
            $panel_options['url'] = $this->getDynamicContent()->getCallbackUrl();
            $panel_options['clearable'] = $this->getDynamicContent()->getClearSelector();
        }

        return $panel_options;
    }

    public function renderView()
    {
        $this->template->trySet('WarningIcon', $this->warningIcon);
        $this->template->trySet('CloseIcon', $this->closeIcon);

        parent::renderView();

        $this->js(true, $this->service()->addPanel($this->getPanelOptions()));
    }
}
