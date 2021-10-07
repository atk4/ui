<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql\Oracle;

use Atk4\Data\Persistence\Sql\Query as BaseQuery;

abstract class AbstractQuery extends BaseQuery
{
    /** @var string */
    protected $template_seq_currval = 'select [sequence].CURRVAL from dual';
    /** @var string */
    protected $template_seq_nextval = '[sequence].NEXTVAL';

    public function render(): string
    {
        if ($this->mode === 'select' && $this->main_table === null) {
            $this->table('DUAL');
        }

        return parent::render();
    }

    /**
     * Set sequence.
     *
     * @param string $sequence
     *
     * @return $this
     */
    public function sequence($sequence)
    {
        $this->args['sequence'] = $sequence;

        return $this;
    }

    public function _render_sequence(): ?string
    {
        return $this->args['sequence'];
    }

    public function groupConcat($field, string $delimiter = ',')
    {
        return $this->expr('listagg({field}, []) within group (order by {field})', ['field' => $field, $delimiter]);
    }
}
