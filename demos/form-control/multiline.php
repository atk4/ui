<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsFunction;
use Atk4\Ui\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Multiline form control', 'icon' => 'database', 'subHeader' => 'Collect/Edit multiple rows of table record.']);

$inventory = new MultilineItem($app->db);
$inventory->getField($inventory->fieldName()->item)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 2]];
$inventory->getField($inventory->fieldName()->inv_date)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 2]];
$inventory->getField($inventory->fieldName()->inv_date)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 2]];
$inventory->getField($inventory->fieldName()->country_id)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 3]];
$inventory->getField($inventory->fieldName()->qty)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 2]];
$inventory->getField($inventory->fieldName()->box)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 2]];
$inventory->getField($inventory->fieldName()->total_sql)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 1, 'class' => 'blue']];
$inventory->getField($inventory->fieldName()->total_php)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 1, 'class' => 'blue']];

$form = Form::addTo($app);

// Add multiline field and set model.
/** @var Form\Control\Multiline */
$multiline = $form->addControl('ml', [Form\Control\Multiline::class, 'tableProps' => ['color' => 'blue'], 'itemLimit' => 10, 'addOnTab' => true]);
$multiline->setModel($inventory);

// Add total field.
$total = 0;
foreach ($inventory as $item) {
    $total += $item->qty * $item->box;
}
$sublayout = $form->layout->addSubLayout([Form\Layout\Section\Columns::class]);
$sublayout->addColumn(12);
$column = $sublayout->addColumn(4);
$controlTotal = $column->addControl('total', ['readOnly' => true])->set($total);

// Update total when qty and box value in any row has changed.
$multiline->onLineChange(function (array $rows, Form $form) use ($controlTotal) {
    $total = 0;
    foreach ($rows as $row => $cols) {
        $total += $cols[MultilineItem::hinting()->fieldName()->qty] * $cols[MultilineItem::hinting()->fieldName()->box];
    }

    return $controlTotal->jsInput()->val($total);
}, [$inventory->fieldName()->qty, $inventory->fieldName()->box]);

$multiline->jsAfterAdd = new JsFunction(['value'], [new JsExpression('console.log(value)')]);
$multiline->jsAfterDelete = new JsFunction(['value'], [new JsExpression('console.log(value)')]);

$form->onSubmit(function (Form $form) use ($multiline) {
    $rows = $multiline->model->atomic(function () use ($multiline) {
        return $multiline->saveRows()->model->export();
    });

    return new JsToast($form->getApp()->encodeJson(array_values($rows)));
});
