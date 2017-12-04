<?php

date_default_timezone_set('UTC');
include 'init.php';

$data = [
    ['id'=>1, 'action'=>'Salary', 'amount'=>200],
    ['id'=> 2, 'action'=>'Purchase goods', 'amount'=>-120],
    ['id'=> 3, 'action'=>'Tax', 'amount'=>-40],
];

$p = new \atk4\data\Persistence_Array($data);
$m = new \atk4\data\Model($p);

$m->addField('action');
$m->addField('amount', ['type'=>'money']);

$table = $app->add('Table');
$table->setModel($m);

$table->template->appendHTML('SubHead', '<tr class="center aligned"><th colspan=2>This is sub-header, goes inside "thead" tag</th></tr>');
$table->template->appendHTML('Body', '<tr class="center aligned"><td colspan=2>This is part of body, goes before other rows</td></tr>');

$table->addHook('beforeRow', function ($table) {
    if ($table->current_id == 2) {
        $table->template->appendHTML('Body', '<tr class="center aligned"><td colspan=2>This goes above row with ID=2 ('.$table->current_row['action'].')</th></tr>');
    } elseif ($table->current_id == 3) {
        $table->renderRow();
        $table->model->set(['action'=>'manually injected row after Tax', 'amount'=>0]);
    }
});

$table->template->appendHTML('Foot', '<tr class="center aligned"><td colspan=2>This will appear above totals</th></tr>');

$table->addTotals(['action' => 'Totals:', 'amount' => ['sum']]);
