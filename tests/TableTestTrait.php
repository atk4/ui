<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Ui\Table;

trait TableTestTrait
{
    /**
     * Extract only <tr> out from a Table given the <tr> data-id attribute value.
     */
    protected function extractTableRow(Table $table, string $rowDataId = '1'): string
    {
        preg_match('~<.*data-id="' . $rowDataId . '".*>~m', $table->render(), $matches);

        return preg_replace('~\r?\n|\r~', '', $matches[0]);
    }

    /**
     * Return column template reference name.
     */
    protected function getColumnRef(Table\Column $column): string
    {
        return 'c_' . $column->shortName;
    }

    /**
     * Return column template class name.
     */
    protected function getColumnClass(Table\Column $column): string
    {
        return '_' . $column->shortName . '_class';
    }

    /**
     * Return column template style name.
     */
    protected function getColumnStyle(Table\Column $column): string
    {
        return '_' . $column->shortName . '_color_rating';
    }
}
