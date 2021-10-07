<?php

declare(strict_types=1);

namespace Atk4\Data\Persistence\Sql\Oracle\Version12;

use Atk4\Data\Persistence\Sql\Oracle\AbstractQuery;

class Query extends AbstractQuery
{
    public function _render_limit(): ?string
    {
        if (!isset($this->args['limit'])) {
            return null;
        }

        $cnt = (int) $this->args['limit']['cnt'];
        $shift = (int) $this->args['limit']['shift'];

        return ' ' . trim(
            ($shift ? 'OFFSET ' . $shift . ' ROWS' : '') .
            ' ' .
            // as per spec 'NEXT' is synonymous to 'FIRST', so not bothering with it.
            // https://docs.oracle.com/javadb/10.8.3.0/ref/rrefsqljoffsetfetch.html
            ($cnt ? 'FETCH NEXT ' . $cnt . ' ROWS ONLY' : '')
        );
    }
}
