<?php

declare(strict_types=1);

namespace atk4\ui\Table\Column;

use atk4\data\Field;
use atk4\ui\Exception;
use atk4\ui\jsExpression;
use atk4\ui\Table;

/**
 * Implements Checkbox column for selecting rows.
 */
class Checkbox extends Table\Column
{
    public $class;

    /**
     * Return action which will calculate and return array of all Checkbox IDs, e.g.
     *
     * [3, 5, 20]
     */
    public function jsChecked()
    {
        return new jsExpression(' $(' . $this->table->jsRender() . ").find('.checked." . $this->class . "').closest('tr').map(function(){ " .
            "return $(this).data('id');}).get().join(',')");
    }

    public function init(): void
    {
        parent::init();
        if (!$this->class) {
            $this->class = 'cb_' . $this->short_name;
        }
    }

    public function getHeaderCellHTML(Field $f = null, $value = null)
    {
        if (isset($f)) {
            throw (new Exception('Checkbox must be placed in an empty column. Don\'t specify any field.'))
                ->addMoreInfo('field', $f);
        }
        $this->table->js(true)->find('.' . $this->class)->checkbox();

        return parent::getHeaderCellHTML($f);
    }

    public function getDataCellTemplate(Field $f = null)
    {
        return $this->app->getTag('div', ['class' => 'ui checkbox ' . $this->class], [['input', ['type' => 'checkbox']]]);
    }
}
