<?php

date_default_timezone_set('UTC');
include 'init.php';

$data = [
    ['id'=>1, 'action'=>'Salary', 'amount'=>200],
    ['id'=> 2, 'action'=>'Purchase goods', 'amount'=>-120],
    ['id'=> 3, 'action'=>'Tax', 'amount'=>-40],
];

$m = new \atk4\data\Model(new \atk4\data\Persistence\Static_($data));
$m->getField('amount')->type = 'money';

// ========================================================
$app->add(['Header', 'Table with various headers', 'subHeader'=>'Demonstrates how you can add subheaders, footnotes and other insertions into your data table', 'icon'=>'table']);

$table = $app->add('Table');
$table->setModel($m, ['action']);
$table->addColumn('amount', ['Money']);

// Table template can be tweaked directly
$table->template->appendHTML('SubHead', '<tr class="center aligned"><th colspan=2>This is sub-header, goes inside "thead" tag</th></tr>');
$table->template->appendHTML('Body', '<tr class="center aligned"><td colspan=2>This is part of body, goes before other rows</td></tr>');

// Hook can be used to display data before row. You can also inject and format extra rows.
$table->addHook('beforeRow', function ($table) {
    if ($table->current_id == 2) {
        $table->template->appendHTML('Body', '<tr class="center aligned"><td colspan=2>This goes above row with ID=2 ('.$table->current_row['action'].')</th></tr>');
    } elseif ($table->current_row['action'] == 'Tax') {
        // renders current row
        $table->renderRow();

        // adjusts data for next render
        $table->model->set(['action'=>'manually injected row after Tax', 'amount'=>-0.02]);
    }
});

$table->template->appendHTML('Foot', '<tr class="center aligned"><td colspan=2>This will appear above totals</th></tr>');
$table->addTotals(['action' => 'Totals:', 'amount' => ['sum']]);

// ========================================================
$app->add(['Header', 'Columns with multiple formats', 'subHeader'=>'Single column can use logic to swap out formatters', 'icon'=>'table']);

$table = $app->add('Table');
$table->setModel($m, ['action']);

// copy of amount through a PHP callback
$m->addExpression('amount_copy', [function ($m) {
    return $m['amount'];
}, 'type'=>'money']);

// column with 2 decorators that stack. Money will use red ink and alignment, format will change text.
$table->addColumn('amount', ['Money']);
$table->addDecorator('amount', ['Template', 'Refunded: {$amount}']);

// column which uses selective format depending on condition
$table->addColumn('amount_copy', ['Multiformat', function ($a, $b) {
    if ($a['amount_copy'] > 0) {
        // Two formatters together
        return ['Link', 'Money'];
    } elseif (abs($a['amount_copy']) < 50) {

        // One formatter, but inject template and some attributes
        return [[
            'Template',
            'too <b>little</b> to <u>matter</u>',
            'attr' => ['all' => ['class' => ['right aligned single line']]],
        ]];
    }

    // Short way is to simply return seed
    return 'Money';
}, 'attr'=>['all'=>['class'=>['right aligned singel line']]]]);

// ========================================================
$app->add(['Header', 'Table with resizable columns', 'subHeader'=>'Just drag column header to resize', 'icon'=>'table']);

$table = $app->add('Table');
$table->setModel($m);
$table->addClass('celled')->resizableColumn();
