<?php

namespace atk4\ui\TableColumn;

/**
 * Formatting action buttons column.
 */
class Delete extends Generic
{
    public function init()
    {
        parent::init();
        $this->table->on('click', 'a.'.$this->short_name, new \atk4\ui\jsExpression('alert("ok")'));
    }

    public function getDataCellHTML(\atk4\data\Field $f = null)
    {
        return $this->getTag('td', 'body', ['a', 'href'=>'#', 'class'=>$this->short_name, ['i', 'class'=>'ui trash icon', '']]);
    }
}
