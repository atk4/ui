<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\WarnDynamicPropertyTrait;

/**
 * Implements structure for js closure.
 */
class JsFunction implements JsExpressionable
{
    use WarnDynamicPropertyTrait;

    /** @var array */
    public $fxArgs;

    /** @var array */
    public $fxStatements = [];

    /** @var bool add preventDefault(event) to generated method */
    public $preventDefault = false;

    /** @var bool add stopPropagation(event) to generated method */
    public $stopPropagation = false;

    /** @var string Indent of target code (not one indent level) */
    public $indent = '    ';

    public function __construct(array $args = [], array $statements = null)
    {
        if ($statements === null) {
            $statements = $args;
            $args = [];
        }

        $this->fxArgs = $args;

        foreach ($statements as $key => $value) {
            if (is_numeric($key)) {
                $this->fxStatements[] = $value;
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function jsRender(): string
    {
        $pre = '';
        if ($this->preventDefault) {
            $this->fxArgs = ['event'];
            $pre .= "\n" . $this->indent . '    event.preventDefault();';
        }
        if ($this->stopPropagation) {
            $this->fxArgs = ['event'];
            $pre .= "\n" . $this->indent . '    event.stopPropagation();';
        }

        $output = 'function (' . implode(', ', $this->fxArgs) . ') {'
            . $pre;
        foreach ($this->fxStatements as $statement) {
            if (!$statement) {
                // null passed
                continue;
            }

            if ($statement instanceof JsChain && !$statement->_chain) {
                // chain contains no statements, so probably is useless
                continue;
            }

            if ($statement instanceof JsExpressionable) {
                $statement = $statement->jsRender();
            } else {
                throw (new Exception('Incorrect statement for JsFunction'))
                    ->addMoreInfo('statement', $statement);
            }

            $output .= "\n" . $this->indent . '    ' . $statement . (!preg_match('~[;}]\s*$~', $statement) ? ';' : '');
        }

        $output .= "\n" . $this->indent . '}';

        return $output;
    }
}
