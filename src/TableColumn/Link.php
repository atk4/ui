<?php

namespace atk4\ui\TableColumn;

use atk4\data\Model;

/**
 * Implements Column helper for grid.
 * Use
 *   new Link('http://yahoo.com?id={$id}');
 * or
 *   new Link(['order', 'id'=>'id' ]);
 * or
 *   new Link(['order', 'id' ]);
 * or
 *   new Link([['order', 'x'=>$myval], 'id' ]);.
 */
class Link extends Generic
{
    /**
     * If $url is set up, we will use pattern-matching to fill-in any part of this
     * with values of the model.
     *
     * @var string|array Destination definition
     */
    public $url;

    /**
     * If string 'example', then will be passed to $app->url('example') along with any defined arguments.
     * If set as arrray, then non-0 key/values will be also passed to the URL:
     *  $page = ['example', 'type'=>'123'];.
     *
     * $url = $app->url(['example', 'type'=>'123']);
     *
     * In addition to abpove "args" refer to values picked up from a current row.
     */
    public $page;

    /**
     * When constructing a URL using 'page', this specifies list of values which will be added
     * to the destination URL. For example if you set $args = ['document_id'=>'id'] then row value
     * of ['id'] will be added to url's property "document_id".
     *
     * For a full example:
     *  $page = ['example', 'type'=>'client'];
     *  $args = ['contact_id'=>'id'];
     *
     * Link URL will be "example.php?type=client&contact_id=4"
     *
     * You can also pass non-key arguments ['id', 'title'] and they will be added up
     * as ?id=4&title=John%20Smith
     */
    public $args = [];

    /** @var bool use value as label of the link */
    public $use_label = true;

    /**
     * set element class.
     *
     * @var string|null
     */
    public $class;

    /**
     * Use icon as label of the link.
     *
     * @var string|null
     */
    public $icon;

    /**
     * set html5 target attribute in tag
     * possible values : _blank | _parent | _self | _top | frame#name.
     *
     * @var string!null
     */
    public $target;

    /**
     * add download in the tag to force download from the url.
     *
     * @var bool
     */
    public $force_download = false;

    public function __construct($page = [], $args = [])
    {
        if (is_array($page)) {
            $page = ['page' => $page];
        } elseif (is_string($page)) {
            $page = ['url' => $page];
        }
        if ($args) {
            $page['args'] = $args;
        }
        parent::__construct($page);
    }

    public function setDefaults(array $properties, bool $passively = false)
    {
        if (isset($properties[0])) {
            $this->page = array_shift($properties);
        }
        if (isset($properties[0])) {
            $this->args = array_shift($properties);
        }
        parent::setDefaults($properties);
    }

    public function init(): void
    {
        parent::init();

        if ($this->url && is_string($this->url)) {
            $this->url = new \atk4\ui\Template($this->url);
        }
        if ($this->page && is_string($this->page)) {
            $this->page = [$this->page];
        }
    }

    public function getDataCellTemplate(\atk4\data\Field $f = null)
    {
        $download = $this->force_download ? ' download="true" ' : '';
        $external = $this->target ? ' target="' . $this->target . '" ' : '';

        $icon = '';

        if ($this->icon) {
            $icon = '<i class="icon ' . $this->icon . '"></i>';
        }

        $label = '';
        if ($this->use_label) {
            $label = $f ? ('{$' . $f->short_name . '}') : '[Link]';
        }

        $class = '';
        if ($this->class) {
            $class = ' class="' . $this->class . '" ';
        }

        return '<a href="{$c_' . $this->short_name . '}"' . $external . $class . $download . '>' . $icon . '' . $label . '</a>';
    }

    public function getHTMLTags(Model $row, $field)
    {
        // Decide on the content
        if ($this->url) {
            $rowValues = $this->app->ui_persistence ? $this->app->ui_persistence->typecastSaveRow($row, $row->get()) : $row->get();

            return ['c_' . $this->short_name => $this->url->set($rowValues)->render()];
        }

        $p = $this->page ?: [];

        foreach ($this->args as $key => $val) {
            if (is_numeric($key)) {
                $key = $val;
            }

            if ($row->hasField($val)) {
                $p[$key] = $row->get($val);
            }
        }

        return ['c_' . $this->short_name => $this->table->url($p)];
    }
}
