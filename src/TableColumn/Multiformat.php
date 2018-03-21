<?php

namespace atk4\ui\TableColumn;

/**
 * Swaps out column decorators based on logic.
 */
class Multiformat extends Generic
{
    public $callback = null;

    public function getDataCellHTML(\atk4\data\Field $f = null)
    {
        return '{$c_'.$this->short_name.'}';
    }

    public function getHTMLTags($row, $field)
    {
        if (!$this->callback) {
            throw new \atk4\ui\Exception(['Must specify a callback for column', 'column'=>$this]);
        }

        $decorators = call_user_func($this->callback, $row, $field);
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
                $html = $c->getDataCellHTML($field, $td_attr);
            }

            if ($cell) {
                if ($name) {
                    // if name is set, we can wrap things
                    $cell = str_replace('{$'.$name.'}', $cell, $html);
                } else {
                    $cell = $cell.' '.$html;
                }
            } else {
                $cell = $html;
            }
            if (!method_exists($c, 'getHTMLTags')) {
                continue;
            }
            $html_tags = array_merge($c->getHTMLTags($row, $field), $html_tags);
        }

        $template = $this->owner->add(['Template', $cell]);
        $template->set($row);
        $template->setHTML($html_tags);

        $val = $template->render();

        return ['c_'.$this->short_name => $val];
    }
}
