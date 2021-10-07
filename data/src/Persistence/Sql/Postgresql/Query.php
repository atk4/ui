<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql\Postgresql;

use Atk4\Data\Persistence\Sql\Expression;
use Atk4\Data\Persistence\Sql\Query as BaseQuery;

class Query extends BaseQuery
{
    protected $template_update = 'update [table][join] set [set] [where]';
    protected $template_replace;

    public function _render_limit(): ?string
    {
        if (!isset($this->args['limit'])) {
            return null;
        }

        return ' limit ' .
            (int) $this->args['limit']['cnt'] .
            ' offset ' .
            (int) $this->args['limit']['shift'];
    }

    public function groupConcat($field, string $delimiter = ','): Expression
    {
        return $this->expr('string_agg({}, [])', [$field, $delimiter]);
    }
}
