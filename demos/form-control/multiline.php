<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsFunction;
use Atk4\Ui\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Multiline form control', 'icon' => 'database', 'subHeader' => 'Collect/Edit multiple rows of table record.']);

/** @var Model $inventoryItemClass */
$inventoryItemClass = AnonymousClassNameCache::get_class(fn () => new class() extends Model {
    public Persistence $countryPersistence;

    protected function init(): void
    {
        parent::init();

        $this->addField('item', [
            'required' => true,
            'default' => 'item',
            'ui' => ['multiline' => [Form\Control\Multiline::TABLE_CELL => ['width' => 2]]],
        ]);
        $this->addField('inv_date', [
            'default' => new \DateTime(),
            'type' => 'date',
            'ui' => ['multiline' => [Form\Control\Multiline::TABLE_CELL => ['width' => 2]]],
        ]);
        $this->addField('inv_time', [
            'default' => new \DateTime(),
            'type' => 'time',
            'ui' => ['multiline' => [Form\Control\Multiline::TABLE_CELL => ['width' => 2]]],
        ]);
        $this->hasOne('country', [
            'model' => new Country($this->countryPersistence),
            'ui' => ['multiline' => [Form\Control\Multiline::TABLE_CELL => ['width' => 3]]],
        ]);
        $this->addField('qty', [
            'type' => 'integer',
            'caption' => 'Qty / Box',
            'default' => 1,
            'required' => true,
            'ui' => ['multiline' => [Form\Control\Multiline::TABLE_CELL => ['width' => 2]]],
        ]);
        $this->addField('box', [
            'type' => 'integer',
            'caption' => '# of Boxes',
            'default' => 1,
            'required' => true,
            'ui' => ['multiline' => [Form\Control\Multiline::TABLE_CELL => ['width' => 2]]],
        ]);
        $this->addExpression('total', [
            'expr' => function (Model $row) {
                return $row->get('qty') * $row->get('box');
            },
            'type' => 'integer',
            'ui' => ['multiline' => [Form\Control\Multiline::TABLE_CELL => ['width' => 1, 'class' => 'blue']]],
        ]);
    }
});

$inventory = new $inventoryItemClass(new Persistence\Array_(), ['countryPersistence' => $app->db]);

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
    $total += $entity->get('qty') * $entity->get('box');
    $entity->saveAndUnload();
}

$form = Form::addTo($app);

// Add multiline field and set model.
/** @var Form\Control\Multiline */
$multiline = $form->addControl('ml', [Form\Control\Multiline::class, 'tableProps' => ['color' => 'blue'], 'itemLimit' => 10, 'addOnTab' => true]);
$multiline->setModel($inventory);

// Add total field.
$sublayout = $form->layout->addSubLayout([Form\Layout\Section\Columns::class]);
$sublayout->addColumn(12);
$column = $sublayout->addColumn(4);
$controlTotal = $column->addControl('total', ['readOnly' => true])->set($total);

// Update total when qty and box value in any row has changed.
$multiline->onLineChange(function (array $rows, Form $form) use ($controlTotal) {
    $total = 0;
    foreach ($rows as $row => $cols) {
        $qty = $cols['qty'] ?? 0;
        $box = $cols['box'] ?? 0;
        $total += $qty * $box;
    }

    return $controlTotal->jsInput()->val($total);
}, ['qty', 'box']);

$multiline->jsAfterAdd = new JsFunction(['value'], [new JsExpression('console.log(value)')]);
$multiline->jsAfterDelete = new JsFunction(['value'], [new JsExpression('console.log(value)')]);

$form->onSubmit(function (Form $form) use ($multiline) {
    $rows = $multiline->saveRows()->model->export();

    return new JsToast($form->getApp()->encodeJson(array_values($rows)));
});
