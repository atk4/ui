<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use atk4\data\Field;
use atk4\data\Model;
use atk4\ui\Exception;
use atk4\ui\Table;

/**
 * Swaps out column decorators based on logic.
 */
class Multiformat extends Table\Column
{
    /**
     * @var \Closure Method to execute which will return array of seeds for decorators
     */
    public $callback;

    public function getDataCellHtml(Field $field = null, $extra_tags = [])
    {
        return '{$c_' . $this->short_name . '}';
    }

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function getHtmlTags(Model $row, $field)
    {
        if (!$this->callback) {
            throw (new Exception('Must specify a callback for column'))
                ->addMoreInfo('column', $this);
        }

        $decorators = ($this->callback)($row, $field);
        if (is_string($decorators)) {
            $decorators = [[$decorators]];
        }

        if (is_object($decorators)) {
            $decorators = [$decorators];
        }

        // we need to smartly wrap things up
        $name = $field->short_name;
        $cell = null;
        $cnt = count($decorators);
        $td_attr = [];
        $html_tags = [];
        foreach ($decorators as $c) {
            if (!is_object($c)) {
                $c = $this->owner->decoratorFactory($field, $c);
            }

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
                    $cell = $cell . ' ' . $html;
                }
            } else {
                $cell = $html;
            }
            if (!method_exists($c, 'getHtmlTags')) {
                continue;
            }
            $html_tags = array_merge($c->getHtmlTags($row, $field), $html_tags);
        }

        $template = new \atk4\ui\Template($cell);
        $template->app = $this->app;
        $template->set($row);
        $template->setHtml($html_tags);

        $val = $template->render();

        return ['c_' . $this->short_name => $val];
    }
}
