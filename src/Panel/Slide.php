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

//    /** @var array The default content seed. */
//    public $defaultContent = [FlyoutContent::class];

    public function init()
    {
        parent::init();
        $this->addPanelContent(new SlideContent());
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
     *
     * @return mixed
     */
    public function jsOpen($jsTrigger, $args = [])
    {
        return $this->service()->openPanel([
                                                'triggered' => $jsTrigger,
                                                'reloadArgs' => $args,
                                                'openId'     => $this->name,
                                            ]);
    }

    /**
     * Will reload panel passing args as Get param via js flyoutService.
     *
     * @param array $args
     *
     * @return mixed
     */
    public function jsPanelReload($args = [])
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
    public function addConfirmation($msg, $onApprove = null, $onDeny = null, $title = 'Changes are not saved!', $okBtn = null, $cancelBtn = null)
    {
        if (!$onDeny) {
            $onDeny = new \atk4\ui\jsExpression('function(){return true;}');
        }

        if(!$onApprove){
            $onApprove = $this->jsCloseFromModal();
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


        $this->closeModal->options['modal_option']['onDeny'] = $onDeny;
        $this->closeModal->options['modal_option']['onApprove'] = $onApprove;

        $this->closeModal->notClosable();
    }

    /**
     * Display a warning sign in slideContent view.
     *
     * @param bool $state
     *
     * @return mixed
     */
    public function jsDisplayWarning(bool $state = true)
    {
        return $this->getSlideContent()->jsDisplayWarning($state);
    }

    /**
     * Return proper js for closing panel from modal.
     */
    public function jsCloseFromModal()
    {
        return new jsFunction([
            $this->service()->doClosePanel($this->name),
            new jsExpression('return true'),
        ]);
    }

    /**
     * @deprecated Used onOpen instead.
     *
     * @param null $callback
     * @param null $junk
     *
     * @return View|void
     */
    public function set($callback = null, $junk = null)
    {
        $this->onOpen($callback);
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


    public function renderView()
    {
        parent::renderView();

        $this->js(
            true,
            $this->service()->addPanel([
                'url'           => $this->getSlideContent()->getCallbackUrl(),
                'modal'         => $this->closeModal ? '#'.$this->closeModal->name : null,
                'id'            => $this->name,
                'warning'       => ['selector' => $this->getSlideContent()->getWarningSelector(), 'trigger' => $this->getSlideContent()->getWarningTrigger()],
                'visible'       => 'atk-visible', // the triggering css class that will make this slide panel visible.
                'closeSelector' => $this->getSlideContent()->getCloseSelector(), // the css selector to close this flyout.
                'loader'        => ['selector' => '.ui.loader', 'trigger' => 'active'], // the css selector and trigger class to activate loader.
                'clearable'     => $this->getSlideContent()->getClearSelector(), // an array of css selector to clear when content reload.
            ])
        );
    }
}
