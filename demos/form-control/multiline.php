<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Form;
use Atk4\Ui\Form\Control\Multiline;
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
    public $countryPersistence;

    protected function init(): void
    {
        parent::init();

        $this->addField('item', [
            'required' => true,
            'default' => 'item',
            'ui' => ['multiline' => [Multiline::TABLE_CELL => ['width' => 2]]],
        ]);
        $this->addField('inv_date', [
            'default' => new \DateTime(),
            'type' => 'date',
            'typecast' => [
                function ($v) {
                    return ($v instanceof \DateTime) ? date_format($v, $this->dateFormat) : $v;
                },
                function ($v) {
                    return $v;
                },
            ],
            'ui' => ['multiline' => [Multiline::TABLE_CELL => ['width' => 2]]],
        ]);
        $this->addField('inv_time', [
            'default' => new \DateTime(),
            'type' => 'time',
            'typecast' => [
                function ($v) {
                    return ($v instanceof \DateTime) ? date_format($v, $this->timeFormat) : $v;
                },
                function ($v) {
                    return $v;
                },
            ],
            'ui' => ['multiline' => [Multiline::TABLE_CELL => ['width' => 2]]],
        ]);
        $this->hasOne('country', [
            'model' => new Country($this->countryPersistence),
            'ui' => ['multiline' => [Multiline::TABLE_CELL => ['width' => 3]]],
        ]);
        $this->addField('qty', [
            'type' => 'integer',
            'caption' => 'Qty / Box',
            'default' => 1,
            'required' => true,
            'ui' => ['multiline' => [Multiline::TABLE_CELL => ['width' => 2]]],
        ]);
        $this->addField('box', [
            'type' => 'integer',
            'caption' => '# of Boxes',
            'default' => 1,
            'required' => true,
            'ui' => ['multiline' => [Multiline::TABLE_CELL => ['width' => 2]]],
        ]);
        $this->addExpression('total', [
            'expr' => function (Model $row) {
                return $row->get('qty') * $row->get('box');
            },
            'type' => 'integer',
            'ui' => ['multiline' => [Multiline::TABLE_CELL => ['width' => 1, 'class' => 'blue']]],
        ]);
    }
});

$inventory = new $inventoryItemClass(new Persistence\Array_(), ['dateFormat' => $dateFormat, 'timeFormat' => $timeFormat, 'countryPersistence' => $app->db]);

// Populate some data.
$total = 0;
for ($i = 1; $i < 3; ++$i) {
    $entity = $inventory->createEntity();
    $entity->set('id', $i);
    $entity->set('inv_date', new \DateTime());
    $entity->set('inv_time', new \DateTime());
    $entity->set('item', 'item_' . $i);
    $entity->set('country', random_int(1, 100));
    $entity->set('qty', random_int(10, 100));
    $entity->set('box', random_int(1, 10));
    $total = $total + ($entity->get('qty') * $entity->get('box'));
    $entity->saveAndUnload();
}

$form = Form::addTo($app);

// Add multiline field and set model.
$multiline = $form->addControl('ml', [Multiline::class, 'tableProps' => ['color' => 'blue'], 'itemLimit' => 10, 'addOnTab' => true]);
$multiline->setModel($inventory);

// Add total field.
$sublayout = $form->layout->addSubLayout([Form\Layout\Section\Columns::class]);
$sublayout->addColumn(12);
$column = $sublayout->addColumn(4);
$controlTotal = $column->addControl('total', ['readonly' => true])->set($total);

// Update total when qty and box value in any row has changed.
$multiline->onLineChange(function ($rows, $form) use ($controlTotal) {
    $total = 0;
    foreach ($rows as $row => $cols) {
        $qty = $cols['qty'] ?? 0;
        $box = $cols['box'] ?? 0;
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
