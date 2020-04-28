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
    public $fx_statements = [];

    /** @var bool add preventDefault(event) to generated method */
    public $preventDefault = false;

    /** @var bool add stopPropagation(event) to generated method */
    public $stopPropagation = false;

    /** @var string Array of statements */
    public $indent = '  ';

    /**
     * Constructor.
     *
     * @param array $args
     * @param array $statements
     *
     * @throws Exception
     */
    public function __construct($args = [], $statements = null)
    {
        if ($statements === null) {
            $statements = $args;
            $args = [];
        }

        $this->fx_args = $args ?: [];

        if (!is_array($statements)) {
            throw new Exception(['$statements is not array', 'statements' => $statements]);
        }

        foreach ($statements as $key => $value) {
            if (is_numeric($key)) {
                $this->fx_statements[] = $value;
            } else {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Render function/expression.
     *
     * @throws Exception
     *
     * @return string
     */
    public function jsRender()
    {
        $pre = '';

        if ($this->preventDefault) {
            $this->fx_args = ['event'];
            $pre .= "\n" . $this->indent . '  event.preventDefault();';
        }

        if ($this->stopPropagation) {
            $this->fx_args = ['event'];
            $pre .= "\n" . $this->indent . '  event.stopPropagation();';
        }

        $output = 'function(' . implode(',', $this->fx_args) . ') {';
        $output .= $pre;
        foreach ($this->fx_statements as $statement) {
            if (!$statement) {
                // null passed
                continue;
            }

            if ($statement instanceof jsChain && !$statement->_chain) {
                // chain contains no statements, so probably is useless
                continue;
            }

            if ($statement instanceof jsExpressionable) {
                $statement = $statement->jsRender();
            } else {
                throw new Exception(['Incorrect statement for jsFunction.', 'statement' => $statement]);
            }

            $output .= "\n" . $this->indent . '  ' . $statement . (!preg_match('~[;}]\s*$~', $statement) ? ';' : '');
        }

        $output .= "\n" . $this->indent . '}';

        return $output;
    }
}
