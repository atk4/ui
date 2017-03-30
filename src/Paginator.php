<?php

namespace atk4\ui;

class Paginator extends View
{
    public $defaultTemplate = 'paginator.html';

    /**
     * Specify how many pages this paginator has total
     */
    public $total = null;

    /**
     * Override what is the current page. If not set, Paginator will look inside
     * $_GET[$this->name]. If page > total, then page = total.
     */
    public $page = null;

    /**
     * Specifies how many items per page must be shown.
     */
    public $ipp = 50;

    /**
     * When there are more than $range*2+1 items, then current page will be surrounded by $range pages
     * followed by spacer ..., for example if range=2, then
     *
     * 1, ..., 5, 6, *7*, 8, 9, ..., 34
     */
    public $range  = 4;

    public $ui = 'pagination menu';

    /**
     * If specified, must be instance of a view which will be reloaded on selection
     */
    public $reload = null;

    function init()
    {
        parent::init();

        if (!$this->page) {
            $this->page = $this->getCurrentPage();
        }
    }

    /**
     * Pages begin with 1
     */
    function getCurrentPage()
    {
        return isset($_GET[$this->name]) ? (int)$_GET[$this->name]: 1;
    }

    function getPaginatorItems()
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

    function url($page) {
        return $this->app->url(['paginator', $this->name=>$page]);
    }

    function renderItem($t, $page = null) {
        if ($page) {
            $t->trySet('page', (string)$page);
            $t->trySet('link', $this->url($page));

            $t->trySet('active', $page === $this->page ? 'active': '');
        }

        $this->template->appendHTML('rows', $t->render());
    }

    function renderView()
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
            $this->on('click', '.item', new jsReload($this->reload, [$this->name => new jsExpression('$(this).data("page")')]));
        }


        parent::renderView();
    }

}
