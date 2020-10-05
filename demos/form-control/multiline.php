<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\data\Model;
use atk4\data\Persistence;
use atk4\ui\Form;
use atk4\ui\Header;
use atk4\ui\JsExpression;
use atk4\ui\JsFunction;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Multiline form control', 'icon' => 'database', 'subHeader' => 'Collect/Edit multiple rows of table record.']);

/** @var Model $inventoryItemClass */
$inventoryItemClass = get_class(new class() extends Model {
    public $dateFormat;

    protected function init(): void
    {
        parent::init();

        $this->addField('item', ['required' => true, 'default' => 'item']);
        $this->addField('inv_date', [
            'default' => date($this->dateFormat),
            'type' => 'date',
            'typecast' => [
                function ($v) {
                    return ($v instanceof \DateTime) ? date_format($v, $this->dateFormat) : $v;
                },
            ],
            'ui' => ['multiline' => ['width' => 3]],
        ]);
        $this->addField('qty', ['type' => 'integer', 'caption' => 'Qty / Box', 'required' => true, 'ui' => ['multiline' => ['width' => 2]]]);
        $this->addField('box', ['type' => 'integer', 'caption' => '# of Boxes', 'required' => true, 'ui' => ['multiline' => ['width' => 2]]]);
        $this->addExpression('total', ['expr' => function (Model $row) {
            return $row->get('qty') * $row->get('box');
        }, 'type' => 'integer']);
    }
});

$inventory = new $inventoryItemClass(new Persistence\Array_(), ['dateFormat' => $app->ui_persistence->date_format]);

// Populate some data.
$total = 0;
for ($i = 1; $i < 3; ++$i) {
    $inventory2 = clone $inventory;
    $inventory2->set('id', $i);
    $inventory2->set('inv_date', date($app->ui_persistence->date_format));
    $inventory2->set('item', 'item_' . $i);
    $inventory2->set('qty', random_int(10, 100));
    $inventory2->set('box', random_int(1, 10));
    $total = $total + ($inventory2->get('qty') * $inventory2->get('box'));
    $inventory2->saveAndUnload();
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

$multiline->jsAfterAdd = new JsFunction(['value'], [new JsExpression('console.log(value)')]);
$multiline->jsAfterDelete = new JsFunction(['value'], [new JsExpression('console.log(value)')]);

$form->onSubmit(function (Form $form) use ($multiline) {
    $rows = $multiline->saveRows()->getModel()->export();

    return new \atk4\ui\JsToast($form->getApp()->encodeJson(array_values($rows)));
});
