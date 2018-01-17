<?php

namespace atk4\ui;

class Paginator extends View
{
    /**
     * Specify how many pages this paginator has in total.
     *
     * @var int
     */
    public $total = null;

    /**
     * Override what is the current page. If not set, Paginator will look inside
     * $_GET[$this->name]. If page > total, then page = total.
     *
     * @var int
     */
    public $page = null;

    /**
     * When there are more than $range*2+1 items, then current page will be surrounded by $range pages
     * followed by spacer ..., for example if range=2, then.
     *
     * 1, ..., 5, 6, *7*, 8, 9, ..., 34
     *
     * @var int
     */
    public $range = 4;

    /**
     * Set this if you want GET argument name to look beautifully.
     *
     * @var null|string
     */
    public $urlTrigger = null;

    /**
     * If specified, must be instance of a view which will be reloaded on selection.
     */
    public $reload = null;

    public $ui = 'pagination menu';
    public $defaultTemplate = 'paginator.html';

    /**
     * Initializing.
     */
    public function init()
    {
        parent::init();

        if (!$this->urlTrigger) {
            $this->urlTrigger = $this->name;
        }

        if (!$this->page) {
            $this->page = $this->getCurrentPage();
        }
    }

    /**
     * Set total number of pages.
     *
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = (int) $total;

        if ($this->total < 1) {
            $this->total = 1;
        }

        if ($this->page < 1) {
            $this->page = 1;
        } elseif ($this->page > $this->total) {
            $this->page = $this->total;
        }
    }

    /**
     * Determine and return the current page. You can extend this method for
     * the advanced logic.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return isset($_GET[$this->urlTrigger]) ? (int) $_GET[$this->urlTrigger] : 1;
    }

    /**
     * Calculate logical sequence of items in a paginator. Responds with array
     * containing recipe for HTML augmenting:.
     *
     * [ '[', '...', 10, 11, 12 ]
     *
     * Array will contain '[', ']', denoting "first" , "last" items, '...' for the spacer and any
     * other integer value for a regular page link.
     *
     * @return array
     */
    public function getPaginatorItems()
    {
        if ($this->page < 1) {
            $this->page = 1;
        } elseif ($this->page > $this->total) {
            $this->page = $this->total;
        }

        $start = $this->page - $this->range;
        $end = $this->page + $this->range;

        // see if we are close to the edge
        if ($start < 1) {
            // shift by ($start-1);
            $end += (1 - $start);
            $start = 1;
        }
        if ($end > $this->total) {
            $start -= ($end - $this->total);
            $end = $this->total;
        }

        if ($start < 1) {
            $start = 1; // shifted twice
        }

        $p = [];

        if ($start > 1) {
            $p[] = '[';
        }

        if ($start > 2) {
            $p[] = '...';
        }

        for ($i = $start; $i <= $end; $i++) {
            $p[] = $i;
        }

        if ($end < $this->total - 1) {
            $p[] = '...';
        }

        if ($end < $this->total) {
            $p[] = ']';
        }

        return $p;
    }

    /**
     * Return URL for displaying a certain page.
     *
     * @param int|string $page
     *
     * @return string
     */
    public function getPageURL($page)
    {
        return $this->url([$this->urlTrigger => $page]);
    }

    /**
     * Render page item using template $t for the page number $page.
     *
     * @param Template   $t
     * @param int|string $page
     */
    public function renderItem($t, $page = null)
    {
        if ($page) {
            $t->trySet('page', (string) $page);
            $t->trySet('link', $this->getPageURL($page));

            $t->trySet('active', $page === $this->page ? 'active' : '');
        }

        $this->template->appendHTML('rows', $t->render());
    }

    public function renderView()
    {
        $t_item = $this->template->cloneRegion('Item');
        $t_first = $this->template->hasTag('FirstItem') ? $this->template->cloneRegion('FirstItem') : $t_item;
        $t_last = $this->template->hasTag('LastItem') ? $this->template->cloneRegion('LastItem') : $t_item;
        $t_spacer = $this->template->cloneRegion('Spacer');

        $this->template->del('rows');

        foreach ($this->getPaginatorItems() as $item) {
            if ($item === '[') {
                $this->renderItem($t_first, 1);
            } elseif ($item === '...') {
                $this->renderItem($t_spacer);
            } elseif ($item === ']') {
                $this->renderItem($t_last, $this->total);
            } else {
                $this->renderItem($t_item, $item);
            }
        }

        if ($this->reload) {
            $this->on('click', '.item', new jsReload($this->reload, [$this->urlTrigger => new jsExpression('$(this).data("page")')]));
        }

        parent::renderView();
    }
}
