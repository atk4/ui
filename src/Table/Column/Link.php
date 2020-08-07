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
 *   new Link('https://yahoo.com?id={$id}');
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
     * @var string|HtmlTemplate
     */
    public $url;

    /**
     * If string 'example', then will be passed to $app->url('example') along with any defined arguments.
     * If set as array, then non-0 key/values will be also passed to the URL:
     *  $page = ['example', 'type' => '123'];.
     *
     * $url = $app->url(['example', 'type' => '123']);
     *
     * In addition to above "args" refer to values picked up from a current row.
     *
     * @var string|array<0|string, string|int|false>|null
     */
    public $page;

    /**
     * When constructing a URL using 'page', this specifies list of values which will be added
     * to the destination URL. For example if you set $args = ['document_id' => 'id'] then row value
     * of ['id'] will be added to URL's property "document_id".
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
     * @var array<int|string, string>
     */
    public $args = [];

    /** @deprecated use $label instead - will be removed dec-2020 */
    public $useLabel = true;

    /**
     * Label template to use on the link.
     *
     * $label = true - use field value as label
     * $label = false - no label
     * $label = 'string {$field}' - use custom template provided
     *
     * @var bool|string
     * */
    public $label = true;

    /** @var string|null set element class. */
    public $class;

    /** @var string|null Use icon as label of the link. */
    public $icon;

    /**
     * Set html5 target attribute in tag
     * possible values: _blank | _parent | _self | _top | frame#name.
     *
     * @var string|null
     */
    public $target;

    /** @var bool add download in the tag to force download from the URL. */
    public $forceDownload = false;

    /**
     * @param string|array<0|string, string|int|false> $page
     */
    public function __construct($page = [], array $args = [], array $defaults = [])
    {
        if (is_array($page)) {
            $defaults['page'] = $page;
        } else {
            $defaults['url'] = $page;
        }

        $defaults['args'] = $args;

        parent::__construct($defaults);
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

    public function getDataCellTemplate(Field $field = null): string
    {
        $attr = ['href' => '{$c_' . $this->shortName . '}'];

        if ($this->forceDownload) {
            $attr['download'] = 'true';
        }

        if ($this->target) {
            $attr['target'] = $this->target;
        }

        $icon = '';
        if ($this->icon) {
            $icon = $this->getApp()->getTag('i', ['class' => $this->icon . ' icon'], '');
        }

        $label = '';
        // backward compatibility - will be removed dec-2020
        if (!$this->useLabel) {
            'trigger_error'('Property atk4\ui\Table\Column\Link::$useLabel is deprecated. Use atk4\ui\Table\Column\Link::$label instead', E_USER_DEPRECATED);

            $this->label = false;
        }

        if ($this->label) {
            if ($this->label === true) {
                $label = $field ? ('{$' . $field->shortName . '}') : '[Link]';
            } else {
                $label = $this->label;
            }
        }

        if ($this->class) {
            $attr['class'] = $this->class;
        }

        return $this->getApp()->getTag('a', $attr, [$icon, $label]); // TODO $label is not HTML encoded
    }

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        if ($this->url) {
            $rowValues = $this->getApp()->uiPersistence->typecastSaveRow($row, $row->get());

            return ['c_' . $this->shortName => $this->url->set($rowValues)->renderToHtml()];
        }

        $page = $this->page ?? [];

        foreach ($this->args as $key => $val) {
            if (is_int($key)) {
                $key = $val;
            }

            $page[$key] = $row->get($val);
        }

        return ['c_' . $this->shortName => $this->table->url($page)];
    }
}
