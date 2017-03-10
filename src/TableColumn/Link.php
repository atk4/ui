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
        $this->page = $page[0];
    }

    /**
     * kill me now for this code :!!
     */
    public function getCellTemplate(\atk4\data\Field $f)
    {
        if (!is_array($this->page)) {
            return $this->app->getTag('td', [], $this->app->getTag('a', ['href'=>$this->page], '{$'.$f->short_name.'}'));
        }
        foreach ($this->page as &$val) {
            $val = str_replace('{$', '___o', $val);
            $val = str_replace('}', 'c___', $val);
        }

        $href = $this->app->url($this->page);
        $output = $this->app->getTag('td', [], $this->app->getTag('a', ['href'=>$href], '{$'.$f->short_name.'}'));

        $output = str_replace('___o', '{$', $output);
        $output = str_replace('c___', '_urlencode}', $output);

        return $output;
    }

    /**
     * Auto-fill tag for the row-id.
     *
     * @param [type] $row   [description]
     * @param [type] $field [description]
     *
     * @return [type] [description]
     */
    public function getHTMLTags($row, $field)
    {
        return ['id_urlencode'=>$row->id];
    }
}
