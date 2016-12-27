<?php

namespace atk4\ui;

/**
 * Implements structure for js closure.
 */
class jsFunction implements jsExpressionable
{
    /** @var array Array of arguments */
    public $fx_args;

    /** @var array Array of statements */
    public $fx_statements;

    /**
     * Constructor.
     *
     * @param array $args
     * @param array $statements
     */
    public function __construct($args = [], $statements = [])
    {
        $this->fx_args = $args;
        $this->fx_statements = $statements;
    }

    /**
     * Render arguments.
     *
     * @param array $args
     *
     * @return string
     */
    private function _renderArgs($args = [])
    {
        if ($args === null) {
            return [];
        }

        return
            array_map(function ($arg) {
                if ($arg instanceof jsExpressionable) {
                    return $arg->jsRender();
                }

                return json_encode($arg);
            }, $args);
    }

    /**
     * Render function/expression.
     *
     * @return string
     */
    public function jsRender()
    {
        return 'function('.implode(',', $this->_renderArgs($this->fx_args)).") {\n".
            implode(";\n", $this->_renderArgs($this->fx_statements)).";\n".
            '}';
    }
}
