<?php

namespace atk4\ui\tests\Concerns;

use atk4\ui\Table;

trait HandlesTable
{
    /**
     * Extract only <tr> out from an atk4\ui\Table given the <tr> data-id attribute value.
     *
     * @param string $rowDataId
     *
     * @return string
     */
    protected function extractTableRow(Table $table, $rowDataId = '1')
    {
        $matches = [];

        preg_match('/<.*data-id="' . $rowDataId . '".*/m', $table->render(), $matches);

        return preg_replace('~\r?\n|\r~', '', $matches[0]);
    }
}
