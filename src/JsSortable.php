<?php

declare(strict_types=1);

namespace Atk4\Ui;

class JsSortable extends JsCallback
{
    /** @var string The html element that contains others element for reordering. */
    public $container = 'tbody';

    /** @var string The html element inside the container that need reordering. */
    public $draggable = 'tr';

    /**
     * The data label set as data-label attribute on the html element.
     *  The callback will send source parameter on the moved element using this attribute.
     *  default to data-id.
     *
     * If the data-{label} attribute is not set for each list element, then the $_POST['order']
     * value will be empty. Only orgIdx and newIdx will be sent in callback request.
     *
     * @var string
     */
    public $dataLabel = 'id';

    /**
     * The css class name of the handle element for dragging purpose.
     * If null, the entire element become the dragging handle.
     *
     * @var string|null
     */
    public $handleClass;

    /** @var bool Whether callback will be fire automatically or not. */
    public $autoFireCb = true;

    /** @var View|null The View that need reordering. */
    public $view;

    protected function init(): void
    {
        parent::init();

        if (!$this->view) {
            $this->view = $this->getOwner();
        }
        $this->getApp()->requireJs($this->getApp()->cdn['atk'] . '/external/@shopify/draggable/lib/draggable.bundle.js');

        $this->view->js(true)->atkJsSortable([
            'url' => $this->getJsUrl(),
            'urlOptions' => $this->args,
            'container' => $this->container,
            'draggable' => $this->draggable,
            'handleClass' => $this->handleClass,
            'dataLabel' => $this->dataLabel,
            'autoFireCb' => $this->autoFireCb,
        ]);
    }

    /**
     * Callback when container has been reorder.
     */
    public function onReorder(\Closure $fx): void
    {
        $this->set(function () use ($fx) {
            $sortOrders = explode(',', $_POST['order']);
            $source = $_POST['source'];
            $newIdx = (int) $_POST['newIdx'];
            $orgIdx = (int) $_POST['orgIdx'];

            return $fx($sortOrders, $source, $newIdx, $orgIdx);
        });
    }

    /**
     * Return js action to retrieve order.
     *
     * @param array|null $urlOptions
     *
     * @return JsChain
     */
    public function jsSendSortOrders($urlOptions = null)
    {
        return $this->view->js()->atkJsSortable('sendSortOrders', [$urlOptions]);
    }
}
