<?php

namespace atk4\ui;

/**
 * Implements structure for js closure
 */
class jsFunction implements jsExpressionable {

    public $fx_args;

    public $fx_statements = [];

    public $preventDefault = false;

    public $stopPropagation = false;

    public $indent = '  ';

    function __construct($args = [], $statements = null)
    {
        if ($statements === null) {
            $statements = $args;
            $args = [];
        }

        $this->fx_args = $args ?: [];

        if (!is_array($statements)) {
            throw new Exception(['$statements is not array', 'statements'=>$statements]);
        }

        foreach($statements as $key=>$value) {
            if (is_numeric($key)) {
                $this->fx_statements[] = $value;
            } else {
                $this->$key = $value;
            }
        }
    }

    function jsRender() {

        $pre = "";

        if ($this->preventDefault) {
            $this->fx_args = ['event'];
            $pre .= "\n".$this->indent."  event.preventDefault();";
        }

        if ($this->stopPropagation) {
            $this->fx_args = ['event'];
            $pre .= "\n".$this->indent."  event.stopPropagation();";
        }

        $output = 'function('.join(',',$this->fx_args).') {';
        $output .= $pre;
        foreach($this->fx_statements as $statement) {

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
                throw new Exception(["Incorrect statement for jsFunction.", 'statement'=>$statement]);
            }

            $output .= "\n".$this->indent."  ".$statement.";";
        }
            
        $output .= "\n".$this->indent."}";

        return $output;
    }
}
