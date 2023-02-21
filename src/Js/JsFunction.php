<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

use Atk4\Core\WarnDynamicPropertyTrait;

class JsFunction implements JsExpressionable
{
    use WarnDynamicPropertyTrait;

    /** @var list<string> */
    public array $args;

    public JsBlock $body;

    /** Add event.preventDefault() to generated method */
    public bool $preventDefault = false;

    /** Add event.stopPropagation() to generated method */
    public bool $stopPropagation = false;

    /** Indent of target code (not one indent level) */
    public string $indent = '';

    /**
     * @param JsBlock|array<int, JsExpressionable|null>|array<string, mixed> $statements
     */
    public function __construct(array $args, $statements)
    {
        $this->args = $args;

        if (!is_array($statements)) {
            $this->body = $statements;
        } else {
            foreach ($statements as $key => $value) {
                if (is_string($key)) {
                    $this->{$key} = $value;
                    unset($statements[$key]);
                } elseif ($value === null) { // TODO this should be not needed
                    unset($statements[$key]);
                }
            }

            $this->body = new JsBlock($statements);
        }
    }

    public function jsRender(): string
    {
        $pre = '';
        if ($this->preventDefault) {
            $this->args = ['event'];
            $pre .= $this->indent . '    event.preventDefault();' . "\n";
        }
        if ($this->stopPropagation) {
            $this->args = ['event'];
            $pre .= $this->indent . '    event.stopPropagation();' . "\n";
        }

        $output = $this->indent . 'function (' . implode(', ', $this->args) . ') {' . "\n"
            . $pre
            . preg_replace('~^~m', $this->indent . '    ', $this->body->jsRender()) . "\n" // TODO IMPORTANT indentation must ignore multiline strings/comments!
            . $this->indent . '}';

        return $output;
    }
}
