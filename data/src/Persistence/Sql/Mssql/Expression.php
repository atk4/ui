<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql\Mssql;

use Atk4\Data\Persistence\Sql\Expression as BaseExpression;

class Expression extends BaseExpression
{
    use ExpressionTrait;

    protected $escape_char = ']';
}
