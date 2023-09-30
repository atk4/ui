<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpressionable;

/**
 * Paginate content using scroll event in JS.
 */
class JsPaginator extends JsCallback
{
    /** @var View|null The View that trigger scrolling event. */
    public $view;

    /**
     * The JS scroll plugin options
     *  - appendTo: the HTML selector where new content should be appendTo.
     *              Ex: For a table, the selector would be 'tbody'.
     *  - padding: Bottom padding need prior to perform a page request.
     *             Page request will be ask when container is scroll down and reach padding value.
     *  - initialPage: The initial page load.
     *                 The next page request will be initialPage + 1.
     *  - hasFixTableHeader: boolean Should we use fixed table header.
     *  - tableContainerHeight: int Fixed height of table container in pixels.
     *  - tableHeaderColor: int|string HTML color for header.
     *
     * @var array
     */
    public $options = [];

    protected function init(): void
    {
        parent::init();

        if (!$this->view) {
            $this->view = $this->getOwner();
        }

        $this->view->js(true)->atkScroll([
            'url' => $this->getJsUrl(),
            'urlOptions' => $this->args,
            'options' => $this->options,
        ]);
    }

    /**
     * Set JsPaginator in idle mode.
     *
     * @return Jquery
     */
    public function jsIdle(): JsExpressionable
    {
        return $this->view->js(true)->atkScroll('idle');
    }

    /**
     * Get current page number.
     */
    public function getPage(): int
    {
        return (int) ($this->getApp()->tryGetRequestQueryParam('page') ?? 0);
    }

    /**
     * Callback when container has been scroll to bottom.
     *
     * @param \Closure(int): (JsExpressionable|View|string|void) $fx
     */
    public function onScroll(\Closure $fx): void
    {
        $page = $this->getPage();
        $this->set(static function () use ($fx, $page) {
            return $fx($page);
        });
    }
}
