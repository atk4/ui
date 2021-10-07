<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql\Oracle;

class Query extends AbstractQuery
{
    // {{{ for Oracle 11 and lower to support LIMIT with OFFSET

    protected $template_select = '[with]select[option] [field] [from] [table][join][where][group][having][order]';
    /** @var string */
    protected $template_select_limit = 'select * from (select "__t".*, rownum "__dsql_rownum" [from] ([with]select[option] [field] [from] [table][join][where][group][having][order]) "__t") where "__dsql_rownum" > [limit_start][and_limit_end]';

    public function limit($cnt, $shift = null)
    {
        // This is for pre- 12c version
        $this->template_select = $this->template_select_limit;

        return parent::limit($cnt, $shift);
    }

    public function _render_limit_start(): string
    {
        return (string) ($this->args['limit']['shift'] ?? 0);
    }

    public function _render_and_limit_end(): ?string
    {
        if (!$this->args['limit']['cnt']) {
            return '';
        }

        return ' and "__dsql_rownum" <= ' .
            max((int) ($this->args['limit']['cnt'] + $this->args['limit']['shift']), (int) $this->args['limit']['cnt']);
    }

    public function getIterator(): \Traversable
    {
        foreach (parent::getIterator() as $row) {
            unset($row['__dsql_rownum']);

            yield $row;
        }
    }

    public function getRows(): array
    {
        return array_map(function ($row) {
            unset($row['__dsql_rownum']);

            return $row;
        }, parent::getRows());
    }

    public function getRow(): ?array
    {
        $row = parent::getRow();

        if ($row !== null) {
            unset($row['__dsql_rownum']);
        }

        return $row;
    }

    /// }}}

    public function exists()
    {
        return $this->dsql()->mode('select')->field(
            $this->dsql()->expr('case when exists[] then 1 else 0 end', [$this])
        );
    }
}
