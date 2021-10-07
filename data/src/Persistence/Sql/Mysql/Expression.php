<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql\Mysql;

use Atk4\Data\Persistence\Sql\Expression as BaseExpression;

class Expression extends BaseExpression
{
    protected $escape_char = '`';
}
