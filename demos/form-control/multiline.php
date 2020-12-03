<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsFunction;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Multiline form control', 'icon' => 'database', 'subHeader' => 'Collect/Edit multiple rows of table record.']);

$dateFormat = $app->ui_persistence->date_format;
$timeFormat = $app->ui_persistence->time_format;

/** @var Model $inventoryItemClass */
$inventoryItemClass = get_class(new class() extends Model {
    public $dateFormat;
    public $timeFormat;

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
        $this->addField('inv_time', [
            'default' => date($this->timeFormat),
            'type' => 'time',
            'typecast' => [
                function ($v) {
                    return ($v instanceof \DateTime) ? date_format($v, $this->timeFormat) : $v;
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

$inventory = new $inventoryItemClass(new Persistence\Array_(), ['dateFormat' => $dateFormat, 'timeFormat' => $timeFormat]);

// Populate some data.
$total = 0;
for ($i = 1; $i < 3; ++$i) {
    $inventory2 = clone $inventory;
    $inventory2->set('id', $i);
    $inventory2->set('inv_date', date($dateFormat));
    $inventory2->set('inv_time', date($timeFormat));
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

    return new \Atk4\Ui\JsToast($form->getApp()->encodeJson(array_values($rows)));
});
