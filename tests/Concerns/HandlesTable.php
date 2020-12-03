<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests\Concerns;

use Atk4\Ui\Table;

trait HandlesTable
{
    /**
     * Extract only <tr> out from an \Atk4\Ui\Table given the <tr> data-id attribute value.
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

    /**
     * Return column template reference name.
     */
    protected function getColumnRef(Table\Column $column): string
    {
        return 'c_' . $column->short_name;
    }

    /**
     * Return column template class name.
     */
    protected function getColumnClass(Table\Column $column): string
    {
        return '_' . $column->short_name . '_class';
    }

    /**
     * return column template style name.
     */
    protected function getColumnStyle(Table\Column $column): string
    {
        return '_' . $column->short_name . '_color_rating';
    }
}
