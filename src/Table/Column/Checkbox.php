<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Ui\Exception;
use Atk4\Ui\JsExpression;
use Atk4\Ui\Table;

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
        return new JsExpression(' $(' . $this->table->jsRender() . ").find('.checked." . $this->class . "').closest('tr').map(function(){ " .
            "return $(this).data('id');}).get().join(',')");
    }

    protected function init(): void
    {
        parent::init();
        if (!$this->class) {
            $this->class = 'cb_' . $this->short_name;
        }
    }

    public function getHeaderCellHtml(Field $field = null, $value = null)
    {
        if (isset($field)) {
            throw (new Exception('Checkbox must be placed in an empty column. Don\'t specify any field.'))
                ->addMoreInfo('field', $field);
        }
        $this->table->js(true)->find('.' . $this->class)->checkbox();

        return parent::getHeaderCellHtml($field);
    }

    public function getDataCellTemplate(Field $field = null)
    {
        return $this->getApp()->getTag('div', ['class' => 'ui checkbox ' . $this->class], [['input', ['type' => 'checkbox']]]);
    }
}
