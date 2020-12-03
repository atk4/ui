<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Paginate content using scroll event in JS.
 */
class JsPaginator extends JsCallback
{
    /**
     * The View that trigger scrolling event.
     *
     * @var View|null
     */
    public $view;

    /**
     * The js scroll plugin options
     *  - appendTo : the html selector where new content should be appendTo.
     *              Ex: For a table, the selector would be 'tbody'.
     *  - padding: Bottom padding need prior to perform a page request.
     *             Page request will be ask when container is scroll down and reach padding value.
     *  - initialPage: The initial page load.
     *                 The next page request will be initialPage + 1.
     *  - allowJsEval: Whether or not you want the plugin to evaluate javascript return by server response.
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
            'uri' => $this->getJsUrl(),
            'uri_options' => $this->args,
            'options' => $this->options,
        ]);
    }

    /**
     * Generate a js action that will set nextPage to atkScroll plugin.
     *
     * Method currently unused.
     *
     * @param int $page
     *
     * @return Jquery
     */
    public function jsNextPage($page)
    {
        return $this->view->js(true)->atkScroll('setNextPage', $page);
    }

    /**
     * Set jsPagiantor in idle mode.
     *
     * @return Jquery
     */
    public function jsIdle()
    {
        return $this->view->js(true)->atkScroll('idle');
    }

    /**
     * Get current page number.
     *
     * @return int
     */
    public function getPage()
    {
        return (int) ($_GET['page'] ?? 0);
    }

    /**
     * Callback when container has been scroll to bottom.
     */
    public function onScroll(\Closure $fx)
    {
        $page = $this->getPage();
        $this->set(function () use ($fx, $page) {
            return $fx($page);
        });
    }
}
