<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql\Mssql;

use Atk4\Data\Persistence\Sql\Query as BaseQuery;

class Query extends BaseQuery
{
    use ExpressionTrait;

    protected $escape_char = ']';

    protected $expression_class = Expression::class;

    protected $template_insert = 'begin try'
        . "\n" . 'insert[option] into [table_noalias] ([set_fields]) values ([set_values])'
        . "\n" . 'end try begin catch if ERROR_NUMBER() = 544 begin'
        . "\n" . 'set IDENTITY_INSERT [table_noalias] on'
        . "\n" . 'insert[option] into [table_noalias] ([set_fields]) values ([set_values])'
        . "\n" . 'set IDENTITY_INSERT [table_noalias] off'
        . "\n" . 'end end catch';

    public function _render_limit(): ?string
    {
        if (!isset($this->args['limit'])) {
            return null;
        }

        $cnt = (int) $this->args['limit']['cnt'];
        $shift = (int) $this->args['limit']['shift'];

        return (!isset($this->args['order']) ? ' order by (select null)' : '')
            . ' offset ' . $shift . ' rows'
            . ' fetch next ' . $cnt . ' rows only';
    }

    public function groupConcat($field, string $delimiter = ',')
    {
        return $this->expr('string_agg({}, \'' . $delimiter . '\')', [$field]);
    }

    public function exists()
    {
        return $this->dsql()->mode('select')->field(
            $this->dsql()->expr('case when exists[] then 1 else 0 end', [$this])
        );
    }
}
