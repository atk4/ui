<?php

namespace atk4\ui\TableColumn;

/**
 * Implements Column helper for grid.
 * Use
 *   new Link('http://yahoo.com?id={$id}');
 * or
 *   new Link(['order', 'id'=>'id' ]);
 * or
 *   new Link(['order', 'id' ]);
 * or 
 *   new Link([['order', 'x'=>$myval], 'id' ]);
 */
class Link extends Generic
{
    /**
     * If $url is set up, we will use pattern-matching to fill-in any part of this
     * with values of the model.
     *
     * @param string|array $page Destination definition
     */
    public $url = null;

    /**
     * If string 'example', then will be passed to $app->url('example') along with any defined arguments.
     * If set as arrray, then non-0 key/values will be also passed to the URL:
     *  $page = ['example', 'type'=>'123'];
     *
     * $url = $app->url(['example', 'type'=>'123']);
     *
     * In addition to abpove "args" refer to values picked up from a current row.
     */
    public $page = null;

    /**
     * When constructing a URL using 'page', this specifies list of values which will be added
     * to the destination URL. For example if you set $args = ['document_id'=>'id'] then row value
     * of ['id'] will be added to url's property "document_id"
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

    public function __construct($page = null, $args = [])
    {
        if (is_array($page)) {
            $page = ['page' => $page];
            unset($page[0]);
        } elseif (is_string($page)) {
            $page = ['url' => $page];
        }
        if ($args) {
            $page['args'] = $args;
        }
        parent::__construct($page);
    }

    public function setDefaults($properties = [], $strict = false) {
        if (isset($properties[0])) {
            $this->page = array_shift($properties);
        }
        if (isset($properties[0])) {
            $this->args = array_shift($properties);
        }
        parent::setDefaults($properties);
    }

    function init() {
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
        return '<a href="{$c_'.$this->short_name.'}">'.($f ? ('{$'.$f->short_name.'}') : '[Link]').'</a>';
    }

    public function getHTMLTags($row, $field)
    {
        // Decide on the content
        if ($this->url) {
            return ['c_'.$this->short_name => $this->url->set($row->get())->render()];
        }

        if ($this->page) {
            $p = $this->page;

            foreach ($this->args as $key=>$val) {
                if (is_numeric($key)) {
                    $key = $val;
                }

                if ($row->hasElement($val)) {
                    $p[$key] = $row[$val];
                }
            }

            return ['c_'.$this->short_name => $this->app->url($p)];
        }

        throw new \atk4\ui\Exception('Link without destination');

    }
}
