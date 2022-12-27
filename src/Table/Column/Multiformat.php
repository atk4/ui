<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Table;

/**
 * Swaps out column decorators based on logic.
 */
class Multiformat extends Table\Column
{
    /** @var \Closure Method to execute which will return array of seeds for decorators */
    public $callback;

    public function __construct(\Closure $callback)
    {
        parent::__construct();

        $this->callback = $callback;
    }

    public function getDataCellHtml(Field $field = null, array $attr = []): string
    {
        return '{$c_' . $this->shortName . '}';
    }

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $decorators = ($this->callback)($row, $field);
        // we need to smartly wrap things up
        $name = $field->shortName;
        $cell = null;
        $cnt = count($decorators);
        $td_attr = [];
        $html_tags = [];
        foreach ($decorators as $c) {
            if (!is_object($c)) {
                $c = $this->getOwner()->decoratorFactory($field, $c);
            }
            $c = Table\Column::assertInstanceOf($c);

            if (--$cnt) {
                $html = $c->getDataCellTemplate($field);
                $td_attr = $c->getTagAttributes('body', $td_attr);
            } else {
                // Last formatter, ask it to give us whole rendering
                $html = $c->getDataCellHtml($field, $td_attr);
            }

            if ($cell) {
                if ($name) {
                    // if name is set, we can wrap things
                    $cell = str_replace('{$' . $name . '}', $cell, $html);
                } else {
                    $cell .= ' ' . $html;
                }
            } else {
                $cell = $html;
            }

            $html_tags = array_merge($c->getHtmlTags($row, $field), $html_tags);
        }

        $template = new HtmlTemplate($cell);
        $template->setApp($this->getApp());
        $template->set($row);
        $template->dangerouslySetHtml($html_tags);

        $val = $template->renderToHtml();

        return ['c_' . $this->shortName => $val];
    }
}
