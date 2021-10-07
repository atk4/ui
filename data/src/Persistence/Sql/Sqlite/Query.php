<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql\Sqlite;

use Atk4\Data\Persistence\Sql\Query as BaseQuery;

class Query extends BaseQuery
{
    protected $template_truncate = 'delete [from] [table_noalias]';

    public function groupConcat($field, string $delimiter = ',')
    {
        return $this->expr('group_concat({}, [])', [$field, $delimiter]);
    }
}
