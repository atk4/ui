<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Data\Model;

class Lister extends View
{
    use \Atk4\Core\HookTrait;

    /** @const string */
    public const HOOK_BEFORE_ROW = self::class . '@beforeRow';
    /** @const string */
    public const HOOK_AFTER_ROW = self::class . '@afterRow';

    /**
     * Lister repeats part of it's template. This property will contain
     * the repeating part. Clones from {row}. If your template does not
     * have {row} tag, then entire template will be repeated.
     *
     * @var HtmlTemplate
     */
    public $t_row;

    /**
     * Lister use this part of template in case there are no elements in it.
     *
     * @var HtmlTemplate|null
     */
    public $t_empty;

    public $defaultTemplate;

    /**
     * A dynamic paginator attach to window scroll event.
     *
     * @var JsPaginator|null
     */
    public $jsPaginator;

    /**
     * The number of item per page for JsPaginator.
     *
     * @var int|null
     */
    public $ipp;

    /** @var Model */
    public $current_row;

    /**
     * Initialization.
     */
    protected function init(): void
    {
        parent::init();

        $this->initChunks();
    }

    /**
     * From the current template will extract {row} into $this->t_row_master and {empty} into $this->t_empty.
     */
    public function initChunks()
    {
        if (!$this->template) {
            throw new Exception('Lister does not have default template. Either supply your own HTML or use "defaultTemplate"=>"lister.html"');
        }

        // empty row template
        if ($this->template->hasTag('empty')) {
            $this->t_empty = $this->template->cloneRegion('empty');
            $this->template->del('empty');
        }

        // data row template
        if ($this->template->hasTag('row')) {
            $this->t_row = $this->template->cloneRegion('row');
            $this->template->del('rows');
        } else {
            $this->t_row = clone $this->template;
            $this->template->del('_top');
        }
    }

    /**
     * Add Dynamic paginator when scrolling content via Javascript.
     * Will output x item in lister set per ipp until user scroll content to the end of page.
     * When this happen, content will be reload x number of items.
     *
     * @param int    $ipp          Number of item per page
     * @param array  $options      an array with js Scroll plugin options
     * @param View   $container    The container holding the lister for scrolling purpose. Default to view owner.
     * @param string $scrollRegion A specific template region to render. Render output is append to container html element.
     *
     * @return $this|void
     */
    public function addJsPaginator($ipp, $options = [], $container = null, $scrollRegion = null)
    {
        $this->ipp = $ipp;
        $this->jsPaginator = JsPaginator::addTo($this, ['view' => $container, 'options' => $options]);

        // set initial model limit. can be overwritten by onScroll
        $this->model->setLimit($ipp);

        // add onScroll callback
        $this->jsPaginator->onScroll(function ($p) use ($ipp, $scrollRegion) {
            // set/overwrite model limit
            $this->model->setLimit($ipp, ($p - 1) * $ipp);

            // render this View (it will count rendered records !)
            $jsonArr = $this->renderToJsonArr(true, $scrollRegion);

            // if there will be no more pages, then replace message=Success to let JS know that there are no more records
            if ($this->_rendered_rows_count < $ipp) {
                $jsonArr['message'] = 'Done'; // Done status means - no more requests from JS side
            }

            // return json response
            $this->getApp()->terminateJson($jsonArr);
        });

        return $this;
    }

    /** @var int This will count how many rows are rendered. Needed for JsPaginator for example. */
    protected $_rendered_rows_count = 0;

    protected function renderView(): void
    {
        if (!$this->template) {
            throw new Exception('Lister requires you to specify template explicitly');
        }

        // if no model is set, don't show anything (even warning)
        if (!$this->model) {
            parent::renderView();

            return;
        }

        // Generate template for data row
        $this->t_row->trySet('_id', $this->name);

        // Iterate data rows
        $this->_rendered_rows_count = 0;

        // TODO we should not iterate using $this->model variable,
        // then also backup/tryfinally would be not needed
        // the same in Table class
        $modelBackup = $this->model;
        try {
            foreach ($this->model as $this->model) {
                $this->current_row = $this->model;
                if ($this->hook(self::HOOK_BEFORE_ROW) === false) {
                    continue;
                }

                $this->renderRow();

                ++$this->_rendered_rows_count;
            }
        } finally {
            $this->model = $modelBackup;
        }

        // empty message
        if (!$this->_rendered_rows_count) {
            if (!$this->jsPaginator || !$this->jsPaginator->getPage()) {
                $empty = isset($this->t_empty) ? $this->t_empty->renderToHtml() : '';
                if ($this->template->hasTag('rows')) {
                    $this->template->dangerouslyAppendHtml('rows', $empty);
                } else {
                    $this->template->dangerouslyAppendHtml('_top', $empty);
                }
            }
        }

        // stop JsPaginator if there are no more records to fetch
        if ($this->jsPaginator && ($this->_rendered_rows_count < $this->ipp)) {
            $this->jsPaginator->jsIdle();
        }

        parent::renderView();
    }

    /**
     * Render individual row. Override this method if you want to do more
     * decoration.
     */
    public function renderRow()
    {
        $this->t_row->trySet($this->current_row);

        $this->t_row->trySet('_title', $this->model->getTitle());
        $this->t_row->trySet('_href', $this->url(['id' => $this->current_row->getId()]));
        $this->t_row->trySet('_id', $this->current_row->getId());

        $html = $this->t_row->renderToHtml();
        if ($this->template->hasTag('rows')) {
            $this->template->dangerouslyAppendHtml('rows', $html);
        } else {
            $this->template->dangerouslyAppendHtml('_top', $html);
        }
    }
}
