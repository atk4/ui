<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;
use atk4\ui\jsExpression;
use atk4\ui\jsFunction;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Header::addTo($app, ['Multiline form control', 'icon' => 'database', 'subHeader' => 'Collect/Edit multiple rows of table record.']);

/** @var \atk4\data\Model $inventoryItemClass */
$inventoryItemClass = get_class(new class() extends \atk4\data\Model {
    public function init(): void
    {
        parent::init();

        $this->addField('item', ['required' => true, 'default' => 'item']);
        $this->addField('qty', ['type' => 'integer', 'caption' => 'Qty / Box', 'required' => true, 'ui' => ['multiline' => ['width' => 2]]]);
        $this->addField('box', ['type' => 'integer', 'caption' => '# of Boxes', 'required' => true, 'ui' => ['multiline' => ['width' => 2]]]);
        $this->addExpression('total', ['expr' => function (\atk4\data\Model $row) {
            return $row->get('qty') * $row->get('box');
        }, 'type' => 'integer']);
    }
});

$inventory = new $inventoryItemClass(new \atk4\data\Persistence\Array_());

// Populate some data.
$total = 0;
for ($i = 1; $i < 3; ++$i) {
    $inventory->set('id', $i);
    $inventory->set('item', 'item_' . $i);
    $inventory->set('qty', random_int(10, 100));
    $inventory->set('box', random_int(1, 10));
    $total = $total + ($inventory->get('qty') * $inventory->get('box'));
    $inventory->saveAndUnload();
}

$form = Form::addTo($app);
$form->addControl('test');
// Add multiline field and set model.
$multiline = $form->addControl('ml', [Form\Control\Multiline::class, 'options' => ['color' => 'blue'], 'rowLimit' => 10, 'addOnTab' => true]);
$multiline->setModel($inventory);

// Add total field.
$sublayout = $form->layout->addSublayout([Form\Layout\Section\Columns::class]);
$sublayout->addColumn(12);
$column = $sublayout->addColumn(4);
$controlTotal = $column->addControl('total', ['readonly' => true])->set($total);

// Update total when qty and box value in any row has changed.
$multiline->onLineChange(function ($rows, $form) use ($controlTotal) {
    $total = 0;
    foreach ($rows as $row => $cols) {
        $qty = array_column($cols, 'qty')[0];
        $box = array_column($cols, 'box')[0];
        $total = $total + ($qty * $box);
    }

    return $controlTotal->jsInput()->val($total);
}, ['qty', 'box']);

$multiline->jsAfterAdd = new jsFunction(['value'], [new jsExpression('console.log(value)')]);
$multiline->jsAfterDelete = new jsFunction(['value'], [new jsExpression('console.log(value)')]);

$form->onSubmit(function (Form $form) use ($multiline) {
    $rows = $multiline->saveRows()->getModel()->export();

    return new \atk4\ui\jsToast(json_encode(array_values($rows)));
});
