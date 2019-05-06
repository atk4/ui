<?php

require 'init.php';

/**
 * Class Inventory Item.
 */
class InventoryItem extends \atk4\data\Model
{
    public function init()
    {
        parent::init();

        $this->addField('item', ['required' => true, 'default' => 'item']);
        $this->addField('qty', ['type' => 'number', 'caption' => 'Qty / Box', 'required' => true, 'ui' => ['multiline' => ['width' => 2]]]);
        $this->addField('box', ['type' => 'number', 'caption' => '# of Boxes', 'required' => true, 'ui' => ['multiline' => ['width' => 2]]]);
        $this->addExpression('total', ['expr' => function ($row) {
            return $row['qty'] * $row['box'];
        }, 'type' => 'number']);
    }
}

$app->add(['Header', 'MultiLine form field', 'icon' => 'database', 'subHeader' => 'Collect/Edit multiple rows of table record.']);

$data = [];

$inventory = new InventoryItem(new \atk4\data\Persistence_Array($data));

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

// Add multiline field and set model.
$ml = $f->addField('ml', ['MultiLine', 'options' => ['color' => 'blue']]);
$ml->setModel($inventory);

// Add total field.
$sub_layout = $f->layout->addSublayout('Columns');
$sub_layout->addColumn(12);
$c = $sub_layout->addColumn(4);
$f_total = $c->addField('total', ['readonly' => true])->set($total);

// Update total when qty and box value in any row has changed.
$ml->onChange(function ($rows) use ($f_total) {
    $total = 0;
    foreach ($rows as $row => $cols) {
        $qty = array_column($cols, 'qty')[0];
        $box = array_column($cols, 'box')[0];
        $total = $total + ($qty * $box);
    }

    return $f_total->jsInput()->val($total);
}, ['qty', 'box']);

$f->onSubmit(function ($f) use ($ml) {
    $rows = $ml->saveRows()->getModel()->export();

    return new \atk4\ui\jsToast(json_encode(array_values($rows)));
});
