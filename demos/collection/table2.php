<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Table;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$data = [
    ['id' => 1, 'action' => 'Salary', 'amount' => 200],
    ['id' => 2, 'action' => 'Purchase goods', 'amount' => -120],
    ['id' => 3, 'action' => 'Tax', 'amount' => -40],
];

$model = new \Atk4\Data\Model(new \Atk4\Data\Persistence\Static_($data));
$model->getField('amount')->type = 'money';

\Atk4\Ui\Header::addTo($app, ['Table with various headers', 'subHeader' => 'Demonstrates how you can add subheaders, footnotes and other insertions into your data table', 'icon' => 'table']);

$table = \Atk4\Ui\Table::addTo($app);
$table->setModel($model, ['action']);
$table->addColumn('amount', [Table\Column\Money::class]);

// Table template can be tweaked directly
$table->template->dangerouslyAppendHtml('SubHead', '<tr class="center aligned"><th colspan=2>This is sub-header, goes inside "thead" tag</th></tr>');
$table->template->dangerouslyAppendHtml('Body', '<tr class="center aligned"><td colspan=2>This is part of body, goes before other rows</td></tr>');

// Hook can be used to display data before row. You can also inject and format extra rows.
$table->onHook(\Atk4\Ui\Lister::HOOK_BEFORE_ROW, function (Table $table) {
    if ($table->current_row->getId() === 2) {
        $table->template->dangerouslyAppendHtml('Body', '<tr class="center aligned"><td colspan=2>This goes above row with ID=2 (' . $table->current_row->get('action') . ')</th></tr>');
    } elseif ($table->current_row->get('action') === 'Tax') {
        // renders current row
        $table->renderRow();

        // adjusts data for next render
        $table->model
            ->set('action', 'manually injected row after Tax')
            ->set('amount', -0.02);
    }
});

$table->template->dangerouslyAppendHtml('Foot', '<tr class="center aligned"><td colspan=2>This will appear above totals</th></tr>');
$table->addTotals(['action' => 'Totals:', 'amount' => ['sum']]);

\Atk4\Ui\Header::addTo($app, ['Columns with multiple formats', 'subHeader' => 'Single column can use logic to swap out formatters', 'icon' => 'table']);

$table = \Atk4\Ui\Table::addTo($app);
$table->setModel($model, ['action']);

// copy of amount through a PHP callback
$model->addExpression('amount_copy', [function (\Atk4\Data\Model $model) {
    return $model->get('amount');
}, 'type' => 'money']);

// column with 2 decorators that stack. Money will use red ink and alignment, format will change text.
$table->addColumn('amount', [Table\Column\Money::class]);
$table->addDecorator('amount', [Table\Column\Template::class, 'Refunded: {$amount}']);

// column which uses selective format depending on condition
$table->addColumn('amount_copy', [Table\Column\Multiformat::class, function ($a, $b) {
    if ($a->get('amount_copy') > 0) {
        // Two formatters together
        return [[Table\Column\Link::class], [Table\Column\Money::class]];
    } elseif (abs($a->get('amount_copy')) < 50) {
        // One formatter, but inject template and some attributes
        return [[
            Table\Column\Template::class,
            'too <b>little</b> to <u>matter</u>',
            'attr' => ['all' => ['class' => ['right aligned single line']]],
        ]];
    }

    // Short way is to simply return seed
    return Table\Column\Money::class;
}, 'attr' => ['all' => ['class' => ['right aligned singel line']]]]);

\Atk4\Ui\Header::addTo($app, ['Table with resizable columns', 'subHeader' => 'Just drag column header to resize', 'icon' => 'table']);

$table = \Atk4\Ui\Table::addTo($app);
$table->setModel($model);
$table->addClass('celled')->resizableColumn();
