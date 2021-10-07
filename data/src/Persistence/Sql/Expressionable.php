<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql;

interface Expressionable
{
    public function getDsqlExpression(Expression $expression): Expression;
}
