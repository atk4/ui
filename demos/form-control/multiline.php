<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\Js\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Multiline form control', 'icon' => 'database', 'subHeader' => 'Collect/Edit multiple rows of table record.']);

$inventory = new MultilineItem($app->db);
$inventory->getField($inventory->fieldName()->item)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 2]];
$inventory->getField($inventory->fieldName()->inv_date)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 2]];
$inventory->getField($inventory->fieldName()->inv_time)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 2]];
$inventory->getField($inventory->fieldName()->country_id)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 3]];
$inventory->getField($inventory->fieldName()->qty)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 2]];
$inventory->getField($inventory->fieldName()->box)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 2]];
$inventory->getField($inventory->fieldName()->total_sql)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 1, 'class' => 'blue']];
$inventory->getField($inventory->fieldName()->total_php)->ui['multiline'] = [Form\Control\Multiline::TABLE_CELL => ['width' => 1, 'class' => 'blue']];

$form = Form::addTo($app);

// add multiline field and set model
/** @var Form\Control\Multiline */
$multiline = $form->addControl('items', [Form\Control\Multiline::class, 'tableProps' => ['color' => 'blue'], 'itemLimit' => 10, 'addOnTab' => true]);
$multiline->setModel($inventory);

// add total field
$total = 0;
foreach ($inventory as $item) {
    $total += $item->qty * $item->box;
}
$sublayout = $form->layout->addSubLayout([Form\Layout\Section\Columns::class]);
$sublayout->addColumn(12);
$column = $sublayout->addColumn(4);
$controlTotal = $column->addControl('total', ['readOnly' => true])->set($total);

// update total when qty and box value in any row has changed
$multiline->onLineChange(static function (array $rows, Form $form) use ($controlTotal) {
    $total = 0;
    foreach ($rows as $row => $cols) {
        $total += $cols[MultilineItem::hinting()->fieldName()->qty] * $cols[MultilineItem::hinting()->fieldName()->box];
    }

    return $controlTotal->jsInput()->val($total);
}, [$inventory->fieldName()->qty, $inventory->fieldName()->box]);

$multiline->jsAfterAdd = new JsFunction(['value'], [new JsExpression('console.log(value)')]);
$multiline->jsAfterDelete = new JsFunction(['value'], [new JsExpression('console.log(value)')]);

$form->onSubmit(static function (Form $form) use ($multiline) {
    $rows = $multiline->model->atomic(static function () use ($multiline) {
        return $multiline->saveRows()->model->export();
    });

    // TODO typecast using https://github.com/atk4/ui/pull/1991 once merged
    foreach ($rows as $kRow => $row) {
        foreach ($row as $kV => $v) {
            if ($v instanceof \DateTime) {
                $rows[$kRow][$kV] = $form->getApp()->uiPersistence->typecastSaveField($multiline->model->getField($kV), $row[$kV]);
            }
        }
    }

    return new JsToast($form->getApp()->encodeJson($rows));
});
