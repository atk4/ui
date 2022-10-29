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
    /** @var string */
    public $class;

    /**
     * Return action which will calculate and return array of all Checkbox IDs, e.g.
     *
     * [3, 5, 20]
     *
     * @return JsExpression
     */
    public function jsChecked()
    {
        return new JsExpression('$(' . $this->table->jsRender() . ').find(\'.checked.' . $this->class . '\').closest(\'tr\').map(function () { '
            . 'return $(this).data(\'id\'); }).get().join(\',\')');
    }

    protected function init(): void
    {
        parent::init();

        if (!$this->class) {
            $this->class = 'cb_' . $this->shortName;
        }
    }

    public function getHeaderCellHtml(Field $field = null, $value = null): string
    {
        if ($field !== null) {
            throw (new Exception('Checkbox must be placed in an empty column, don\'t specify any field'))
                ->addMoreInfo('field', $field);
        }
        $this->table->js(true)->find('.' . $this->class)->checkbox();

        $this->table->on('change', 'th', new JsExpression("$(" . $this->table->jsRender() . ").find('tbody .checkbox').checkbox([] ? 'check' : 'uncheck');",
        [new JsExpression('$(' . $this->table->jsRender() . ').find(\'#' . $this->name . '-master-checkbox' . '\').find(\'.checkbox\').checkbox(\'is checked\')')]));

        return $this->getTag('head', [['div', ['class' => 'ui checkbox ' . $this->class], [['input/', ['type' => 'checkbox']]]]], ['id' => $this->name . '-master-checkbox']);
    }

    public function getDataCellTemplate(Field $field = null): string
    {
        return $this->getApp()->getTag('div', ['class' => 'ui checkbox ' . $this->class], [['input/', ['type' => 'checkbox']]]);
    }
}
