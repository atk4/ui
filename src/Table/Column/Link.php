<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Table;

/**
 * Implements Column helper for grid.
 * Use
 *   new Link('http://yahoo.com?id={$id}');
 * or
 *   new Link(['order', 'id' => 'id' ]);
 * or
 *   new Link(['order', 'id' ]);
 * or
 *   new Link([['order', 'x' => $myval], 'id']);.
 */
class Link extends Table\Column
{
    /**
     * If $url is set up, we will use pattern-matching to fill-in any part of this
     * with values of the model.
     *
     * @var string|HtmlTemplate Destination definition
     */
    public $url;

    /**
     * If string 'example', then will be passed to $app->url('example') along with any defined arguments.
     * If set as arrray, then non-0 key/values will be also passed to the URL:
     *  $page = ['example', 'type' => '123'];.
     *
     * $url = $app->url(['example', 'type' => '123']);
     *
     * In addition to abpove "args" refer to values picked up from a current row.
     *
     * @var string|array
     */
    public $page;

    /**
     * When constructing a URL using 'page', this specifies list of values which will be added
     * to the destination URL. For example if you set $args = ['document_id' => 'id'] then row value
     * of ['id'] will be added to url's property "document_id".
     *
     * For a full example:
     *  $page = ['example', 'type' => 'client'];
     *  $args = ['contact_id' => 'id'];
     *
     * Link URL will be "example.php?type=client&contact_id=4"
     *
     * You can also pass non-key arguments ['id', 'title'] and they will be added up
     * as ?id=4&title=John%20Smith
     *
     * @var array
     */
    public $args = [];

    /** @var bool use value as label of the link */
    public $useLabel = true;

    /** @var string|null set element class. */
    public $class;

    /** @var string|null Use icon as label of the link. */
    public $icon;

    /**
     * Set html5 target attribute in tag
     * possible values : _blank | _parent | _self | _top | frame#name.
     *
     * @var string!null
     */
    public $target;

    /** @var bool add download in the tag to force download from the url. */
    public $forceDownload = false;

    /**
     * @param string|array $page
     */
    public function __construct($page = [], array $args = [], array $defaults = [])
    {
        if (is_array($page)) {
            $page = ['page' => $page];
        } elseif (is_string($page)) {
            $page = ['url' => $page];
        }

        if ($args) {
            $page['args'] = $args;
        }

        parent::__construct(array_replace($defaults, $page));
    }

    protected function init(): void
    {
        parent::init();

        if (is_string($this->url)) {
            $this->url = new HtmlTemplate($this->url);
        }
        if (is_string($this->page)) {
            $this->page = [$this->page];
        }
    }

    public function getDataCellTemplate(Field $field = null)
    {
        $download = $this->forceDownload ? ' download="true" ' : '';
        $external = $this->target ? ' target="' . $this->target . '" ' : '';

        $icon = '';

        if ($this->icon) {
            $icon = '<i class="icon ' . $this->icon . '"></i>';
        }

        $label = '';
        if ($this->useLabel) {
            $label = $field ? ('{$' . $field->shortName . '}') : '[Link]';
        }

        $class = '';
        if ($this->class) {
            $class = ' class="' . $this->class . '" ';
        }

        return '<a href="{$c_' . $this->shortName . '}"' . $external . $class . $download . '>' . $icon . '' . $label . '</a>';
    }

    public function getHtmlTags(Model $row, ?Field $field)
    {
        if ($this->url) {
            $rowValues = $this->getApp()->uiPersistence->typecastSaveRow($row, $row->get());

            return ['c_' . $this->shortName => $this->url->set($rowValues)->renderToHtml()];
        }

        $p = $this->page ?: [];

        foreach ($this->args as $key => $val) {
            if (is_numeric($key)) {
                $key = $val;
            }

            $p[$key] = $row->get($val);
        }

        return ['c_' . $this->shortName => $this->table->url($p)];
    }
}
