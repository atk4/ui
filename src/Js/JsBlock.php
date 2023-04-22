<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

use Atk4\Core\WarnDynamicPropertyTrait;

/**
 * @phpstan-consistent-constructor
 */
class JsBlock implements JsExpressionable
{
    use WarnDynamicPropertyTrait;

    /** @var list<JsExpressionable> */
    private array $statements;

    /**
     * @param list<JsExpressionable|null> $statements
     */
    public function __construct(array $statements = [])
    {
        $this->statements = [];
        foreach ($statements as $value) {
            if ($value === null) { // TODO this should be not needed
                continue;
            }

            $this->addStatement($value);
        }
    }

    /**
     * @return list<JsExpressionable>
     */
    public function getStatements(): array
    {
        return $this->statements;
    }

    public function addStatement(JsExpressionable $statement): void
    {
        $this->statements[] = $statement;
    }

    /**
     * @param list<JsExpressionable|null>|JsExpressionable $statements
     *
     * @return static
     */
    public static function fromHookResult($statements)
    {
        return new static(is_array($statements) ? $statements : [$statements]);
    }

    public function jsRender(): string
    {
        $output = '';
        foreach ($this->statements as $statement) {
            $js = $statement->jsRender();
            if ($js === '') {
                continue;
            } elseif (!$statement instanceof self && !preg_match('~;\s*$~', $js)) {
                $js .= ';';
            }

            $output .= ($output !== '' ? "\n" : '') . $js;
        }

        return $output;
    }
}
