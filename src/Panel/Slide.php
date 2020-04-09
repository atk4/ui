<?php
/**
 * Generic Panel implementation.
 * Opening, closing and loading Panel content is manage
 * via the js panel service.
 *
 * Content is loaded via PanelContent View property.
 * This view must implement a callback for content to be add via the callback function.
 */

namespace atk4\ui\Panel;

use atk4\ui\Button;
use atk4\ui\jQuery;
use atk4\ui\jsExpression;
use atk4\ui\jsFunction;
use atk4\ui\Modal;
use atk4\ui\View;

class Slide extends View implements Slidable
{
    public $defaultTemplate = 'panel/slide.html';

    /** @var null  */
    public $closeModal = null;
    /** @var array Confirmation Modal default */
    public $defaultModal = [Modal::class, 'class' => ['mini']];

    /** @var View|null The content to display inside flyout */
    protected $slideContent = null;

    /** @var bool can be closed via esc or by clicking outside panel. */
    protected $hasClickAway = true;

    /** @var array The default content seed. */
    public $defaultContent = [SlideContent::class];

    /** @var string The css selector on where to add close panel event triggering for closing it. */
    public $closeSelector = '.atk-panel-close';

    public $warningSelector = '.atk-panel-warning';
    public $warningTrigger  = 'atk-visible';
    public $warningIcon = 'icon exclamation circle';

    public function init()
    {
        parent::init();
        $this->addPanelContent($this->factory($this->defaultContent));
    }


    public function addPanelContent(SlidableContent $content)
    {
        $this->slideContent = $this->add($content);
    }

    public function getSlideContent(): SlidableContent
    {
        return $this->slideContent;
    }

    /**
     * Return js expression in order to retrieve panelService.
     *
     * @return mixed
     */
    public function service()
    {
        return (new \atk4\ui\jsChain('atk.panelService'));
    }

    /**
     * Return js expression need to open panel via js panelService.
     *
     * @param jsExpression  $jsTrigger  jsExpression that trigger flyout to open.
     * @param array         $args       The data attribute name to include in reload from the triggering element.
     * @param string|null   $activeCss  The css class name to apply on triggering element when panel is open.
     *
     * @return mixed
     *
     */
    public function jsOpen(jsExpression $jsTrigger, array $args = [], string $activeCss = null)
    {
        return $this->service()->openPanel([
            'triggered' => $jsTrigger,
            'reloadArgs' => $args,
            'openId'     => $this->name,
            'activeCSS'  => $activeCss
        ]);
    }

    /**
     * Will reload panel passing args as Get param via js flyoutService.
     *
     * @param array $args
     *
     * @return mixed
     */
    public function jsPanelReload(array $args = [])
    {
        return $this->service()->reloadPanel($this->name, $args);
    }

    /**
     * Return js expression need to close panel via js panelService.
     *
     * @return mixed
     */
    public function jsClose()
    {
        return $this->service()->closePanel($this->name);
    }

    /**
     * Attach confirmation modal view to display.
     * js flyoutService will prevent closing of Flyout if a confirmation modal
     * is attached to it and flyoutService detect that the current open flyoutContent has warning on.
     *
     * @param $msg
     * @param $onApprove
     * @param null $onDeny
     * @param string $title
     * @param null $okBtn
     * @param null $cancelBtn
     *
     * @throws \atk4\core\Exception
     * @throws \atk4\ui\Exception
     */
    public function addConfirmation(
        string $msg,
        jsExpression $onApprove = null,
        jsExpression $onDeny = null,
        string $title = 'Changes are not saved!',
        string $okBtn = null,
        string  $cancelBtn = null)
    {
        if (!$onDeny) {
            $onDeny = new \atk4\ui\jsExpression('function(){return true;}');
        }

        if(!$onApprove){
            $onApprove = $this->jsModalApprove();
        }

        if (!$okBtn) {
            $okBtn = (new Button(['Ok']))->addClass('ok');
        }

        if (!$cancelBtn) {
            $cancelBtn = (new Button(['Cancel']))->addClass('cancel');
        }
        $this->closeModal = $this->app->add( array_merge($this->defaultModal, ['title' => $title]));
        $this->closeModal->add([View::class, $msg, 'element' => 'p']);
        $this->closeModal->addButtonAction($this->factory($okBtn));
        $this->closeModal->addButtonAction($this->factory($cancelBtn));


//        $this->closeModal->options['modal_option']['onDeny'] = $onDeny;
//        $this->closeModal->options['modal_option']['onApprove'] = $onApprove;

        $this->closeModal->notClosable();
    }

    /**
     * Display a warning sign in slideContent view.
     *
     * @param bool $state
     *
     * @return mixed
     */
//    public function jsDisplayWarning(bool $state = true) :jsExpression
//    {
//        return $this->getSlideContent()->jsDisplayWarning($state);
//    }

    /**
     * Return proper js for closing panel from modal.
     */
    public function jsModalApprove()
    {
        return new jsFunction([
            $this->service()->doModalApprove($this->name),
            new jsExpression('return true'),
        ]);
    }

    /**
     * Callback to execute when flyout open.
     * Differ the callback execution to the FlyoutContent.
     *
     * @param callable $callback
     */
    public function onOpen(callable $callback)
    {
        $this->getSlideContent()->onLoad($callback);
    }

    /**
     * Display or not a Warning sign in Panel.
     *
     * @param bool   $state
     * @param string $selector
     *
     * @return jQuery
     */
    public function jsDisplayWarning(bool $state = true ) :jsExpression
    {
        $chain = new jQuery('#' . $this->name . ' ' . $this->warningSelector);

        return $state ? $chain->addClass($this->warningTrigger) : $chain->removeClass($this->warningTrigger);
    }

    public function jsToggleWarning()
    {
        return (new jQuery('#' . $this->name . ' ' . $this->warningSelector))->toggleClass($this->warningTrigger);
    }


    public function renderView()
    {
        $this->template->trySet('WarningIcon', $this->warningIcon);

        parent::renderView();


        $this->js(
            true,
            $this->service()->addPanel([
                'url'           => $this->getSlideContent()->getCallbackUrl(),
                'modal'         => $this->closeModal ? '#'.$this->closeModal->name : null,
                'id'            => $this->name,
                'warning'       => ['selector' => $this->warningSelector, 'trigger' => $this->warningTrigger],
                'visible'       => 'atk-visible', // the triggering css class that will make this slide panel visible.
                'closeSelector' => $this->closeSelector, // the css selector to close this flyout.
                'loader'        => ['selector' => '.ui.loader', 'trigger' => 'active'], // the css selector and trigger class to activate loader.
                'clearable'     => $this->getSlideContent()->getClearSelector(), // an array of css selector to clear when content reload.
                'hasClickAway'  => $this->hasClickAway,
            ])
        );
    }
}
