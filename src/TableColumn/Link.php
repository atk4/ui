<?php

namespace atk4\ui\TableColumn;

/**
 * Implements Column helper for grid.
 */
class Link extends Generic
{
    /**
     * TODO: Refactor so that this goes away!!
     */
    public $page = null;

    /**
     * Use
     *   new Link('http://yahoo.com?id={$id}');
     * or
     *   new Link(['order', 'id'=>'{$id}']);.
     *
     * @param string|array $page Destination definition
     */
    public function __construct($page = [])
    {
        if (!is_array($page)) {
            $page = [$page];
        }
        $this->page = $page;
    }

    /**
     * kill me now for this code :!!
     */
    public function getCellTemplate(\atk4\data\Field $f = null)
    {
        if (is_null($f)) {
            $f = $this;
        }

        foreach ($this->page as &$val) {
            $val = str_replace('{$', '___o', $val);
            $val = str_replace('}', 'c___', $val);
        }

        $href = $this->app->url($this->page);
        $output = $this->getTag('td', 'body', ['a', 'href'=>$href, '{$'.$f->short_name.'}']);

        $output = str_replace('___o', '{$', $output);
        $output = str_replace('c___', '_urlencode}', $output);

        return $output;
    }

    public function getHTMLTags($row, $field)
    {
        return ['id_urlencode'=>$row->id];
    }
}
