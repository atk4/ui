<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsReload;

class Paginator extends View
{
    public $ui = 'pagination menu';
    public $defaultTemplate = 'paginator.html';

    /** Specify how many pages this paginator has in total. */
    public int $total;

    /**
     * Override what is the current page. If not set, Paginator will look inside
     * $_GET[self::$urlTrigger]. If page > total, then page = total.
     */
    public ?int $page = null;

    /**
     * When there are more than ($range * 2 + 1) items, then current page will be surrounded by $range pages
     * followed by spacer ..., for example if range=2, then.
     *
     * 1, ..., 5, 6, *7*, 8, 9, ..., 34
     */
    public int $range = 4;

    /** @var string|null Set this if you want GET argument name to look beautifully. */
    public $urlTrigger;

    /**
     * If specified, must be instance of a view which will be reloaded on click.
     * Otherwise will use link to current page.
     *
     * @var View|null
     */
    public $reload;

    /**
     * Add extra parameter to the reload view
     * as JsReload urlOptions.
     */
    public array $reloadArgs = [];

    protected function init(): void
    {
        parent::init();

        if ($this->urlTrigger === null) {
            $this->urlTrigger = $this->name;
        }

        if (!$this->page) {
            $this->page = $this->getCurrentPage();
        }
    }

    /**
     * Set total number of pages.
     */
    public function setTotal(int $total): void
    {
        $this->total = $total < 1 ? 1 : $total;

        if ($this->page < 1) {
            $this->page = 1;
        } elseif ($this->page > $this->total) {
            $this->page = $this->total;
        }
    }

    /**
     * Determine and return the current page. You can extend this method for
     * the advanced logic.
     */
    public function getCurrentPage(): int
    {
        return (int) ($this->getApp()->tryGetRequestQueryParam($this->urlTrigger) ?? 1);
    }

    /**
     * Calculate logical sequence of items in a paginator. Responds with array
     * containing recipe for HTML augmenting:.
     *
     * [ '[', '...', 10, 11, 12 ]
     *
     * Array will contain '[', ']', denoting "first", "last" items, '...' for the spacer and any
     * other integer value for a regular page link.
     */
    public function getPaginatorItems(): array
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

        for ($i = $start; $i <= $end; ++$i) {
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
     */
    protected function getPageUrl($page): string
    {
        return $this->url([$this->urlTrigger => $page]);
    }

    /**
     * Add extra argument to the reload view.
     * These arguments will be set as urlOptions to JsReload.
     *
     * @param array $args
     */
    public function addReloadArgs($args): void
    {
        $this->reloadArgs = array_merge($this->reloadArgs, $args);
    }

    /**
     * Render page item using template $t for the page number $page.
     *
     * @param HtmlTemplate $t
     * @param int|string   $page
     */
    public function renderItem($t, $page = null): void
    {
        if ($page) {
            $t->set('page', (string) $page);
            $t->set('link', $this->getPageUrl($page));

            $t->trySet('active', $page === $this->page ? 'active' : '');
        }

        $this->template->dangerouslyAppendHtml('rows', $t->renderToHtml());
    }

    protected function renderView(): void
    {
        $tItem = $this->template->cloneRegion('Item');
        $tFirst = $this->template->hasTag('FirstItem') ? $this->template->cloneRegion('FirstItem') : $tItem;
        $tLast = $this->template->hasTag('LastItem') ? $this->template->cloneRegion('LastItem') : $tItem;
        $tSpacer = $this->template->cloneRegion('Spacer');

        $this->template->del('rows');

        foreach ($this->getPaginatorItems() as $item) {
            if ($item === '[') {
                $this->renderItem($tFirst, 1);
            } elseif ($item === '...') {
                $this->renderItem($tSpacer);
            } elseif ($item === ']') {
                $this->renderItem($tLast, $this->total);
            } else {
                $this->renderItem($tItem, $item);
            }
        }

        if ($this->reload) {
            $this->on('click', '.item', new JsReload($this->reload, array_merge([$this->urlTrigger => new JsExpression('$(this).data(\'page\')')], $this->reloadArgs)));
        }

        parent::renderView();
    }
}
