<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Ui\Exception;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
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
     */
    public function jsChecked(): JsExpressionable
    {
        return (new Jquery($this->table))->find('.checked.' . $this->class)->closest('tr')
            ->map(new \Atk4\Ui\Js\JsFunction([], [new JsExpression('return $(this).data(\'id\')')]))
            ->get()->join(',');
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
        $this->table->js(true, new JsExpression('atk.gridCheckboxHelper.masterCheckbox();'));

        return $this->getTag('head', [['div', ['class' => 'ui master fitted checkbox ' . $this->class], [['input/', ['type' => 'checkbox']]]]], ['class' => ['collapsing']]);
    }

    public function getDataCellTemplate(Field $field = null): string
    {
        $this->table->js(true, new JsExpression('atk.gridCheckboxHelper.childCheckbox();'));

        return $this->getApp()->getTag('div', ['class' => 'ui child fitted checkbox ' . $this->class], [['input/', ['type' => 'checkbox']]]);
    }
}
