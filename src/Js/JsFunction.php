<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

use Atk4\Core\WarnDynamicPropertyTrait;

/**
 * Implements structure for js closure.
 */
class JsFunction implements JsExpressionable
{
    use WarnDynamicPropertyTrait;

    /** @var array */
    public $args;

    /** @var array<int, JsExpressionable> */
    public $statements = [];

    /** @var bool add preventDefault(event) to generated method */
    public $preventDefault = false;

    /** @var bool add stopPropagation(event) to generated method */
    public $stopPropagation = false;

    /** @var string Indent of target code (not one indent level) */
    public $indent = '    ';

    /**
     * @param array<int, JsExpressionable|null>|array<string, mixed> $statements
     */
    public function __construct(array $args, array $statements)
    {
        $this->args = $args;

        foreach ($statements as $key => $value) {
            if (is_int($key)) {
                if ($value === null) { // TODO this should be not needed
                    continue;
                }

                $this->statements[] = $value;
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function jsRender(): string
    {
        $pre = '';
        if ($this->preventDefault) {
            $this->args = ['event'];
            $pre .= "\n" . $this->indent . '    event.preventDefault();';
        }
        if ($this->stopPropagation) {
            $this->args = ['event'];
            $pre .= "\n" . $this->indent . '    event.stopPropagation();';
        }

        $output = 'function (' . implode(', ', $this->args) . ') {'
            . $pre;
        foreach ($this->statements as $statement) {
            $js = $statement->jsRender();

            $output .= "\n" . $this->indent . '    ' . $js . (!preg_match('~[;}]\s*$~', $js) ? ';' : '');
        }

        $output .= "\n" . $this->indent . '}';

        return $output;
    }
}
