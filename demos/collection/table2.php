<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Header;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Lister;
use Atk4\Ui\Table;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$data = [
    ['id' => 1, 'action' => 'Salary', 'amount' => 200],
    ['id' => 2, 'action' => 'Purchase goods', 'amount' => -120],
    ['id' => 3, 'action' => 'Tax', 'amount' => -40],
];

$model = new Model(new Persistence\Static_($data));
$model->getField('amount')->type = 'atk4_money';

Header::addTo($app, ['Table with various headers', 'subHeader' => 'Demonstrates how you can add subheaders, footnotes and other insertions into your data table', 'icon' => 'table']);

$table = Table::addTo($app);
$table->setModel($model, ['action']);
$table->addColumn('amount', [Table\Column\Money::class]);

// Table template can be tweaked directly
$table->template->dangerouslyAppendHtml('SubHead', $app->getTag('tr', ['class' => 'center aligned'], [['th', ['colspan' => '2'], 'This is sub-header, goes inside "thead" tag']]));
$table->template->dangerouslyAppendHtml('Body', $app->getTag('tr', ['class' => 'center aligned'], [['td', ['colspan' => '2'], 'This is part of body, goes before other rows']]));

// Hook can be used to display data before row. You can also inject and format extra rows.
$table->onHook(Lister::HOOK_BEFORE_ROW, static function (Table $table) {
    if ($table->currentRow->getId() === 2) {
        $table->template->dangerouslyAppendHtml('Body', $table->getApp()->getTag('tr', ['class' => 'center aligned'], [['td', ['colspan' => '2'], 'This goes above row with ID=2 (' . $table->currentRow->get('action') . ')']]));
    } elseif ($table->currentRow->get('action') === 'Tax') {
        $table->renderRow();

        // adjusts data for next render
        $table->model
            ->set('action', 'manually injected row after Tax')
            ->set('amount', -0.02);
    }
});

$table->template->dangerouslyAppendHtml('Foot', $app->getTag('tr', ['class' => 'center aligned'], [['td', ['colspan' => '2'], 'This will appear above totals']]));
$table->addTotals([
    'action' => 'Totals:',
    'amount' => ['sum'],
]);

Header::addTo($app, ['Columns with multiple formats', 'subHeader' => 'Single column can use logic to swap out formatters', 'icon' => 'table']);

$table = Table::addTo($app);
$table->setModel($model, ['action']);

// copy of amount through a PHP callback
$model->addExpression('amount_copy', ['expr' => static function (Model $model) {
    return $model->get('amount');
}, 'type' => 'atk4_money']);

// column with 2 decorators that stack. Money will use red ink and alignment, format will change text.
$table->addColumn('amount', [Table\Column\Money::class]);
$table->addDecorator('amount', [Table\Column\Template::class, 'Refunded: {$amount}']);

// column which uses selective format depending on condition
$table->addColumn('amount_copy', [Table\Column\Multiformat::class, static function (Model $row) {
    if ($row->get('amount_copy') > 0) {
        // two formatters together
        return [[Table\Column\Link::class], [Table\Column\Money::class]];
    } elseif (abs($row->get('amount_copy')) < 50) {
        // one formatter, but inject template and some attributes
        return [[
            Table\Column\Template::class,
            'too <b>little</b> to <u>matter</u>',
            'attr' => ['all' => ['class' => ['right aligned single line']]],
        ]];
    }

    // one formatter
    return [[Table\Column\Money::class]];
}, 'attr' => ['all' => ['class' => ['right aligned single line']]]]);

Header::addTo($app, ['Table with resizable columns', 'subHeader' => 'Just drag column header to resize', 'icon' => 'table']);

$table = Table::addTo($app);
$table->setModel($model);
$table->addClass('celled')->resizableColumn(static function (Jquery $j, array $data) use ($app) {
    $res = [];
    foreach ($data as $column) {
        $res[$column['column']] = $column['size'] < 100 ? 'narrow' : 'wide';
    }

    return new JsToast('New widths: ' . $app->encodeJson($res));
}, [200, 200, 200]);
