<?php

use atk4\ui\jsExpression;
use atk4\ui\jsFunction;

require __DIR__ . '/init.php';

/**
 * Class Inventory Item.
 */
class InventoryItem extends \atk4\data\Model
{
    public function init()
    {
        parent::init();

        $this->addField('item', ['required' => true, 'default' => 'item']);
        $this->addField('qty', ['type' => 'integer', 'caption' => 'Qty / Box', 'required' => true, 'ui' => ['multiline' => ['width' => 2]]]);
        $this->addField('box', ['type' => 'integer', 'caption' => '# of Boxes', 'required' => true, 'ui' => ['multiline' => ['width' => 2]]]);
        $this->addExpression('total', ['expr' => function ($row) {
            return $row['qty'] * $row['box'];
        }, 'type' => 'integer']);
    }
}

$app->add(['Header', 'MultiLine form field', 'icon' => 'database', 'subHeader' => 'Collect/Edit multiple rows of table record.']);

$data = [];

$inventory = new InventoryItem(new \atk4\data\Persistence\Array_($data));

// Populate some data.
$total = 0;
for ($i = 1; $i < 3; $i++) {
    $inventory['id'] = $i;
    $inventory['item'] = 'item_'.$i;
    $inventory['qty'] = rand(10, 100);
    $inventory['box'] = rand(1, 10);
    $total = $total + ($inventory['qty'] * $inventory['box']);
    $inventory->saveAndUnload();
}

$f = $app->add('Form');
$f->addField('test');
// Add multiline field and set model.
$ml = $f->addField('ml', ['MultiLine', 'options' => ['color' => 'blue'], 'rowLimit' => 4, 'addOnTab' => true]);
$ml->setModel($inventory);

// Add total field.
$sub_layout = $f->layout->addSublayout('Columns');
$sub_layout->addColumn(12);
$c = $sub_layout->addColumn(4);
$f_total = $c->addField('total', ['readonly' => true])->set($total);

// Update total when qty and box value in any row has changed.
$ml->onLineChange(function ($rows, $form) use ($f_total) {
    $total = 0;
    foreach ($rows as $row => $cols) {
        $qty = array_column($cols, 'qty')[0];
        $box = array_column($cols, 'box')[0];
        $total = $total + ($qty * $box);
    }

    return $f_total->jsInput()->val($total);
}, ['qty', 'box']);

$ml->jsAfterAdd = new jsFunction(['value'], [new jsExpression('console.log(value)')]);
$ml->jsAfterDelete = new jsFunction(['value'], [new jsExpression('console.log(value)')]);

$f->onSubmit(function ($f) use ($ml) {
    $rows = $ml->saveRows()->getModel()->export();

    return new \atk4\ui\jsToast(json_encode(array_values($rows)));
});
