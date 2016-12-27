<?php

namespace atk4\ui;

/**
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 */
class jsExpression implements jsExpressionable
{
    public $template = null;

    public $args = [];

    public function __construct($template = '', $args = [])
    {
        $this->template = $template;
        $this->args = $args;
    }

    /**
     * Converts this arbitrary JavaScript expression into string.
     */
    public function jsRender()
    {
        $nameless_count = 0;

        $res = preg_replace_callback(
            '/\[[a-z0-9_]*\]|{[a-z0-9_]*}/',
            function ($matches) use (&$nameless_count) {
                $identifier = substr($matches[0], 1, -1);

                // Allow template to contain []
                if ($identifier === '') {
                    $identifier = $nameless_count++;

                    // use rendering only with named tags
                }

                if (!isset($this->args[$identifier])) {
                    throw new Exception([
                        'Tag not defined in template for jsExpression',
                        'tag'     => $identifier,
                        'template'=> $this->template,
                    ]);
                }

                $value = $this->args[$identifier];

                if (is_object($value) && $value instanceof jsExpressionable) {
                    $value = '('.$value->jsRender().')';
                } elseif (is_object($value)) {
                    $value = json_encode($value->toString());
                } else {
                    $value = json_encode($value);
                }

                return $value;
            },
            $this->template
        );

        return trim($res);
    }
}
