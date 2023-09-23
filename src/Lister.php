<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;

class Lister extends View
{
    use HookTrait;

    public const HOOK_BEFORE_ROW = self::class . '@beforeRow';
    public const HOOK_AFTER_ROW = self::class . '@afterRow';

    public $ui = 'list';

    public $defaultTemplate;

    /**
     * Lister repeats part of it's template. This property will contain
     * the repeating part. Clones from {row}. If your template does not
     * have {row} tag, then entire template will be repeated.
     *
     * @var HtmlTemplate
     */
    public $tRow;

    /** @var HtmlTemplate|null Lister use this part of template in case there are no elements in it. */
    public $tEmpty;

    /** @var JsPaginator|null A dynamic paginator attach to window scroll event. */
    public $jsPaginator;

    /** @var int|null The number of item per page for JsPaginator. */
    public $ipp;

    /** @var Model Current row entity */
    public $currentRow;

    protected function init(): void
    {
        parent::init();

        $this->initChunks();
    }

    /**
     * From the current template will extract {row} into $this->tRowMaster and {empty} into $this->tEmpty.
     */
    protected function initChunks(): void
    {
        if (!$this->template) {
            throw new Exception('Lister does not have default template. Either supply your own HTML or use "defaultTemplate" => "lister.html"');
        }

        // empty row template
        if ($this->template->hasTag('empty')) {
            $this->tEmpty = $this->template->cloneRegion('empty');
            $this->template->del('empty');
        }

        // data row template
        if ($this->template->hasTag('row')) {
            $this->tRow = $this->template->cloneRegion('row');
            $this->template->del('rows');
        } else {
            $this->tRow = clone $this->template;
            $this->template->del('_top');
        }
    }

    /**
     * Add Dynamic paginator when scrolling content via Javascript.
     * Will output x item in lister set per IPP until user scroll content to the end of page.
     * When this happen, content will be reload x number of items.
     *
     * @param int    $ipp          Number of item per page
     * @param array  $options      an array with JS Scroll plugin options
     * @param View   $container    the container holding the lister for scrolling purpose
     * @param string $scrollRegion A specific template region to render. Render output is append to container HTML element.
     *
     * @return $this
     */
    public function addJsPaginator($ipp, $options = [], $container = null, $scrollRegion = null)
    {
        $this->ipp = $ipp;
        $this->jsPaginator = JsPaginator::addTo($this, ['view' => $container, 'options' => $options]);

        // set initial model limit. can be overwritten by onScroll
        $this->model->setLimit($ipp);

        // add onScroll callback
        $this->jsPaginator->onScroll(function (int $p) use ($ipp, $scrollRegion) {
            // set/overwrite model limit
            $this->model->setLimit($ipp, ($p - 1) * $ipp);

            // render this View (it will count rendered records !)
            $jsonArr = $this->renderToJsonArr($scrollRegion);

            // let client know that there are no more records
            $jsonArr['noMoreScrollPages'] = $this->_renderedRowsCount < $ipp;

            // return JSON response
            $this->getApp()->terminateJson($jsonArr);
        });

        return $this;
    }

    /** @var int This will count how many rows are rendered. Needed for JsPaginator for example. */
    protected $_renderedRowsCount = 0;

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

        // iterate data rows
        $this->_renderedRowsCount = 0;

        // TODO we should not iterate using $this->model variable,
        // then also backup/tryfinally would be not needed
        // the same in Table class
        $modelBackup = $this->model;
        $tRowBackup = $this->tRow;
        try {
            foreach ($this->model as $this->model) {
                $this->currentRow = $this->model;
                $this->tRow = clone $tRowBackup;
                if ($this->hook(self::HOOK_BEFORE_ROW) === false) {
                    continue;
                }

                $this->renderRow();

                ++$this->_renderedRowsCount;
            }
        } finally {
            $this->model = $modelBackup;
            $this->tRow = $tRowBackup;
        }

        // empty message
        if ($this->_renderedRowsCount === 0) {
            if (!$this->jsPaginator || !$this->jsPaginator->getPage()) {
                $empty = $this->tEmpty !== null ? $this->tEmpty->renderToHtml() : '';
                if ($this->template->hasTag('rows')) {
                    $this->template->dangerouslyAppendHtml('rows', $empty);
                } else {
                    $this->template->dangerouslyAppendHtml('_top', $empty);
                }
            }
        }

        // stop JsPaginator if there are no more records to fetch
        if ($this->jsPaginator && ($this->_renderedRowsCount < $this->ipp)) {
            $this->jsPaginator->jsIdle();
        }

        parent::renderView();
    }

    /**
     * Render individual row. Override this method if you want to do more
     * decoration.
     */
    public function renderRow(): void
    {
        $this->tRow->trySet($this->currentRow);

        if ($this->tRow->hasTag('_title')) {
            $this->tRow->set('_title', $this->model->getTitle());
        }
        if ($this->tRow->hasTag('_href')) {
            $this->tRow->set('_href', $this->url(['id' => $this->currentRow->getId()]));
        }
        $this->tRow->trySet('_id', $this->name . '-' . $this->currentRow->getId());

        $html = $this->tRow->renderToHtml();
        if ($this->template->hasTag('rows')) {
            $this->template->dangerouslyAppendHtml('rows', $html);
        } else {
            $this->template->dangerouslyAppendHtml('_top', $html);
        }
    }

    /**
     * Hack - override parent method with region render only support.
     *
     * TODO this hack/method must be removed as rendering HTML only partially but with all JS
     * is wrong by design. Each table row should be probably rendered natively using cloned
     * render tree (instead of cloned template).
     */
    public function renderToJsonArr(string $region = null): array
    {
        $this->renderAll();

        // https://github.com/atk4/ui/issues/1932
        if ($region !== null) {
            if (!isset($this->_jsActions['click'])) {
                $this->_jsActions['click'] = [];
            }
            array_unshift($this->_jsActions['click'], $this->js()->off());
        }

        return [
            'success' => true,
            'atkjs' => $this->getJs(),
            'html' => $this->template->renderToHtml($region),
            'id' => $this->name,
        ];
    }
}
