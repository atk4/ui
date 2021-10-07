<?php

declare(strict_types=1);

namespace Atk4\Data;

use Atk4\Core\InitializerTrait;
use Atk4\Data\Persistence\Sql\Expression;

class FieldSqlExpression extends FieldSql
{
    use InitializerTrait {
        init as _init;
    }

    /**
     * Used expression.
     *
     * @var \Closure|string|Expression
     */
    public $expr;

    /**
     * Expressions are always read_only.
     *
     * @var bool
     */
    public $read_only = true;

    /**
     * Specifies how to aggregate this.
     *
     * @var string
     */
    public $aggregate;

    /**
     * Aggregation by concatenation.
     *
     * @var string
     */
    public $concat;

    /**
     * When defining as aggregate, this will point to relation object.
     *
     * @var Reference\HasMany|null
     */
    public $aggregate_relation;

    /**
     * Specifies which field to use.
     *
     * @var string
     */
    public $field;

    /**
     * Initialization.
     */
    protected function init(): void
    {
        $this->_init();

        if ($this->getOwner()->reload_after_save === null) {
            $this->getOwner()->reload_after_save = true;
        }

        if ($this->concat) {
            $this->onHookShortToOwner(Model::HOOK_AFTER_SAVE, \Closure::fromCallable([$this, 'afterSave']));
        }
    }

    /**
     * Possibly that user will attempt to insert values here. If that is the case, then
     * we would need to inject it into related hasMany relationship.
     */
    public function afterSave(): void
    {
    }

    /**
     * Should this field use alias?
     * Expression fields always need alias.
     */
    public function useAlias(): bool
    {
        return true;
    }

    /**
     * When field is used as expression, this method will be called.
     */
    public function getDsqlExpression(Expression $expression): Expression
    {
        $expr = $this->expr;
        if ($expr instanceof \Closure) {
            $expr = $expr($this->getOwner(), $expression);
        }

        if (is_string($expr)) {
            // If our Model has expr() method (inherited from Persistence\Sql) then use it
            if ($this->getOwner()->hasMethod('expr')) {
                return $this->getOwner()->expr('([])', [$this->getOwner()->expr($expr)]);
            }

            // Otherwise call it from expression itself
            return $expression->expr('([])', [$expression->expr($expr)]);
        }

        return $expr;
    }
}
