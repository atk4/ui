<?php

namespace atk4\ui;

class Lister extends View
{
    use \atk4\core\HookTrait;

    /**
     * Lister repeats part of it's template. This property will contain
     * the repeating part. Clones from {row}. If your template does not
     * have {row} tag, then entire template will be repeated.
     *
     * @var Template
     */
    public $t_row_master = null;
    public $t_row = null;

    /**
     * Lister use this part of template in case there are no elements in it.
     *
     * @var null|Template
     */
    public $t_empty;

    public $defaultTemplate = null;

    /**
     * A dynamic paginator attach to window scroll event.
     *
     * @var null|jsPaginator
     */
    public $jsPaginator = null;

    /**
     * The number of item per page for jsPaginator.
     *
     * @var null|int
     */
    public $ipp = null;

    /**
     * Initialization.
     */
    public function init()
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
            throw new Exception(['Lister does not have default template. Either supply your own HTML or use "defaultTemplate"=>"lister.html"']);
        }

        // empty row template
        if ($this->template->hasTag('empty')) {
            $this->t_empty = $this->template->cloneRegion('empty');
            $this->template->del('empty');
        }

        // data row template
        if ($this->template->hasTag('row')) {
            $this->t_row_master = $this->template->cloneRegion('row');
            $this->template->del('rows');
        } else {
            $this->t_row_master = clone $this->template;
            $this->template->del('_top');
        }
    }

    /**
     * Add Dynamic paginator when scrolling content via Javascript.
     * Will output x item in lister set per ipp until user scroll content to the end of page.
     * When this happen, content will be reload x number of items.
     *
     * @param int    $ipp          Number of item per page
     * @param array  $options      An array with js Scroll plugin options.
     * @param View   $container    The container holding the lister for scrolling purpose. Default to view owner.
     * @param string $scrollRegion A specific template region to render. Render output is append to container html element.
     *
     * @throws Exception
     *
     * @return $this|void
     */
    public function addJsPaginator($ipp, $options = [], $container = null, $scrollRegion = null)
    {
        $this->ipp = $ipp;
        $this->jsPaginator = $this->add(['jsPaginator', 'view' => $container, 'options' => $options]);

        // set initial model limit. can be overwritten by onScroll
        $this->model->setLimit($ipp);

        // add onScroll callback
        $this->jsPaginator->onScroll(function ($p) use ($ipp, $scrollRegion) {
            // set/overwrite model limit
            $this->model->setLimit($ipp, ($p - 1) * $ipp);

            // render this View (it will count rendered records !)
            $json = $this->renderJSON(true, $scrollRegion);

            // if there will be no more pages, then replace message=Success to let JS know that there are no more records
            if ($this->_rendered_rows_count < $ipp) {
                $json = json_decode($json, true);
                $json['message'] = 'Done'; // Done status means - no more requests from JS side
                $json = json_encode($json);
            }

            // return json response
            $this->app->terminate($json);
        });

        return $this;
    }

    /** @var int This will count how many rows are rendered. Needed for jsPaginator for example. */
    protected $_rendered_rows_count = 0;

    public function renderView()
    {
        if (!$this->template) {
            throw new Exception(['Lister requires you to specify template explicitly']);
        }

        // if no model is set, don't show anything (even warning)
        if (!$this->model) {
            return parent::renderView();
        }

        // Generate template for data row
        $this->t_row_master->trySet('_id', $this->name);
        $this->t_row = clone $this->t_row_master;

        // Iterate data rows
        $this->_rendered_rows_count = 0;
        foreach ($this->model as $this->current_id => $this->current_row) {
            if ($this->hook('beforeRow') === false) {
                continue;
            }

            $this->renderRow();

            $this->_rendered_rows_count++;
        }

        // empty message
        if (!$this->_rendered_rows_count) {
            if (!$this->jsPaginator || !$this->jsPaginator->getPage()) {
                $empty = isset($this->t_empty) ? $this->t_empty->render() : '';
                if ($this->template->hasTag('rows')) {
                    $this->template->appendHTML('rows', $empty);
                } else {
                    $this->template->appendHTML('_top', $empty);
                }
            }
        }

        // stop jsPaginator if there are no more records to fetch
        if ($this->jsPaginator && ($this->_rendered_rows_count < $this->ipp)) {
            $this->jsPaginator->jsIdle();
        }

        return parent::renderView(); //$this->template->render();
    }

    /**
     * Render individual row. Override this method if you want to do more
     * decoration.
     */
    public function renderRow()
    {
        $this->t_row->trySet($this->current_row);

        $this->t_row->trySet('_title', $this->model->getTitle());
        $this->t_row->trySet('_href', $this->url(['id'=>$this->current_id]));
        $this->t_row->trySet('_id', $this->current_id);

        $html = $this->t_row->render();
        if ($this->template->hasTag('rows')) {
            $this->template->appendHTML('rows', $html);
        } else {
            $this->template->appendHTML('_top', $html);
        }
    }
}
