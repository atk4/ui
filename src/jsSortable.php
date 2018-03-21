<?php

namespace atk4\ui;

class jsSortable extends jsCallback
{
    /**
     * The html element that contains others element for reordering.
     *
     * @var string
     */
    public $container = 'tbody';

    /**
     * The html element inside the container that need reordering.
     *
     * @var string
     */
    public $draggable = 'tr';

    /**
     * The data label set as data-label attribute on the html element.
     *  The callback will send source parameter on the moved element using this attribute.
     *  default to data-id.
     *
     * @var string
     */
    public $dataLabel = 'id';

    /**
     * The css class name of the handle element for dragging purpose.
     *   if null, the entire element become the dragging handle.
     *
     * @var null|string
     */
    public $handleClass = null;

    /**
     * Whether callback will be fire automatically or not.
     *
     * @var bool
     */
    public $autoFireCb = true;

    /**
     * The View that need reordering.
     *
     * @var null| \atk4\ui\View
     */
    public $view = null;

    public function init()
    {
        parent::init();
        if (!$this->view) {
            $this->view = $this->owner;
        }
        $this->app->requireJS('https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.5/lib/draggable.bundle.js');

        $this->view->js(true)->atkJsSortable(['uri'                 => $this->getJSURL(),
                                                      'uri_options' => $this->args,
                                                      'container'   => $this->container,
                                                      'draggable'   => $this->draggable,
                                                      'handleClass' => $this->handleClass,
                                                      'dataLabel'   => $this->dataLabel,
                                                      'autoFireCb'  => $this->autoFireCb,
                                              ]);
    }

    /**
     * Callback when container has been reorder.
     *
     * @param null|callable $fx
     */
    public function onReorder($fx = null)
    {
        if (is_callable($fx)) {
            if ($this->triggered()) {
                $sortOrders = explode(',', @$_POST['order']);
                $source = @$_POST['source'];
                $pos = @$_POST['pos'];
                $this->set(function () use ($fx, $sortOrders, $source, $pos) {
                    return call_user_func_array($fx, [$sortOrders, $source, $pos]);
                });
            }
        }
    }

    /**
     * return js action to retrieve order.
     *
     * @param null|array $uriOptions
     *
     * @return mixed
     */
    public function jsGetOrders($uriOptions = null)
    {
        return $this->view->js()->atkJsSortable('getSortOrders', [$uriOptions]);
    }
}
