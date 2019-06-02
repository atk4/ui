<?php

require 'init.php';
require 'database.php';

$m = new \atk4\invoice\Model\Invoice();

$t = $app->add('Table');

$t->setModel($m);

//$app->add(new \atk4\invoice\InvoiceMgr([
//    'invoiceModel' => new \atk4\invoice\Model\Invoice($db ),
//    'itemRef' => 'Items',
//    'itemLink' => 'invoice_id',
//    'paymentModel' =>  new \atk4\invoice\Model\Payment($db),
//    'tableFields' => ['reference', 'client', 'date', 'due_date', 'total', 'balance'],
//    'headerFields' => ['reference', 'date', 'due_date', 'client_id', 'paid_total'],
//    'footerFields' => ['subtotal', 'tax', 'total'],
//    'itemFields'   => ['item', 'qty', 'rate', 'amount'],
//    'paymentRelations' => ['invoice_id' => 'id', 'client_id' => 'client_id'],
//    'paymentEditFields' => ['method', 'paid_on', 'amount', 'details'],
//    'paymentDisplayFields' => ['client','balance', 'paid_total','total'],
//     ]));

//
//$invoice_model = new \atk4\invoice\Model\Invoice($db, ['taxRate'=> 0.1]);
//$payment_model = new \atk4\invoice\Model\Payment($db);
//
//$invoice = $app->add(new \atk4\invoice\Invoice([
//       'model'          => $invoice_model,
//       'tableFields'    => ['reference', 'client', 'date', 'due_date', 'g_total', 'balance'],
//       'hasPayment'     => true
//    ]));
//
//// set page for editing invoice.
//$invoice->setInvoicePage(function($page, $id) use ($app, $invoice, $invoice_model) {
//
//    $crumb = $page->add(['BreadCrumb',null, 'big']);
//
//    $page->add(['ui' =>'divider']);
//
//    $crumb->addCrumb('Invoices', $invoice->getURL());
//
//    $m = $page->add('Menu');
//    $m->addItem(['Payments', 'icon' => 'dollar sign'])->link($invoice->paymentPage->getURL());
//
//    $form = $page->add(['Form', 'canLeave' => false]);
//
//    if ($id) {
//        $invoice_model->load($id);
//        $crumb->addCrumb($invoice_model->getTitle());
//    } else {
//        $crumb->addCrumb('New Invoice');
//    }
//    $crumb->popTitle();
//
//    $form->add(['Button', 'Back'])->link($invoice->url());
//
//    $m = $form->setModel($invoice_model, false);
//
//    $headerLayout = $form->layout->addSubLayout('Generic');
//    $headerGroup = $headerLayout->addGroup();
//    $headerGroup->setModel($m, ['reference', 'date', 'due_date', 'bill_to_id', 'paid_total']);
//
//    $itemLayout = $form->layout->addSubLayout('Generic');
//    $itemLayout->add(['Header', 'Invoice Items', 'size' => 4]);
//
//    $ml = $itemLayout->addField('ml', ['MultiLine', 'options' => ['size' => 'small']]);
//    $ml->setModel($m, ['item', 'qty', 'rate', 'amount'], 'Items', 'invoice_id');
//
//    $ml->onLineChange([$invoice_model, 'jsUpdateFields'], ['qty', 'rate']);
//
//
//    $columnsLayout = $form->layout->addSubLayout('Columns');
//    $columnsLayout->addColumn(12);
//    $c = $columnsLayout->addColumn(4);
//    $c->setModel($m, ['sub_total', 'tax', 'g_total']);
//
//    $form->onSubmit(function($f) use ($ml, $app) {
//        $f->model->save();
//        $ml->saveRows();
//        return new \atk4\ui\jsToast('Saved!');
//    });
//});
//
//$invoice->setPrintPage(function($page, $id) use ($app, $invoice, $invoice_model) {
//    $invoice_items = $invoice_model->load($id)->ref('Items');
//    $container = $page->add('View')->setStyle(['width' => '900px', 'margin-top' => '20px']);
//    $gl_top = $container->add(['GridLayout', ['rows' => 5, 'columns' => 2]])->setStyle(['width' => '900px', 'margin-top' => '20px']);
//
//    $t = $invoice->getDir('template').'/company.html';
//
//    $comp_view = $gl_top->add(['View', 'defaultTemplate' => $t], 'r1c1');
//    $comp_view->template->set('name', 'My Company');
//    $comp_view->template->set('image', $invoice->getDir('public').'/images/logo.png');
//
//    $inv_info = $gl_top->add('View', 'r1c2');
//    $inv_info->add(['Header', 'Invoice', 'subHeader' => '#'.$invoice_model->getTitle()])->addClass('aligned right');
//    $inv_info->add(['Header', 'Balance', 'size' => 3, 'subHeader' => $invoice->get('balance')])->addClass('aligned right');
//
//    $bill_to = $container->add(['View', 'ui' => 'basic segment']);
//    $bill_to->add(['Header', 'Bill to: '.$invoice_model->get('client'), 'size'=> 4]);
//    $table_view  = $container->add(['View']);
//    $table = $table_view->add('Table')->setModel($invoice_items);
//
//    $container->add(['ui' => 'hidden divider']);
//
//    $gl_bottom = $container->add(['GridLayout', ['rows' => 1, 'columns' => 4]]);
//    $card_container = $gl_bottom->add(['View', 'ui' => 'aligned right'], 'r1c4');
//    $card = $card_container->add(['Card', 'header' => false]);
//    $card->setModel($invoice_model, ['sub_total', 'tax', 'g_total', 'balance']);
//});
//
//// set payment page.
//$invoice->setPaymentPage(function($page, $id) use ($app, $invoice, $payment_model, $invoice_model) {
//    $invoice_model->load($id);
//    $payment_model->addCondition('invoice_id', $id);
//
//    $balance = 'Balance: '.$invoice->get('balance');
//
//    // setup payment editing page.
//    $paymentEdit = $page->add(['VirtualPage', 'urlTrigger' => 'p-edit']);
//    $editCrumb = $paymentEdit->add(['BreadCrumb', null, 'big']);
//    $paymentEdit->add(['ui' =>'divider']);
//
//    $paymentEdit->add(['Header', $balance]);
//    $editCrumb->addCrumb('Invoices', 'invoice-addon.php');
//    $editCrumb->addCrumb($invoice_model->getTitle().' \'s payments', $invoice->paymentPage->getURL());
//
//    $pId = $page->stickyGet('pId');
//    if ($pId) {
//        $payment_model->load($pId);
//        $editCrumb->addCrumb('Edit payment');
//    } else {
//        $editCrumb->addCrumb('New payment');
//    }
//    $editCrumb->popTitle();
//
//    $formPayment = $paymentEdit->add('Form');
//    $formPayment->setModel($payment_model, ['method', 'paid_on', 'amount', 'details']);
//    $formPayment->onSubmit(function($f) use ($app, $invoice, $invoice_model) {
//       $f->model['invoice_id'] =  $invoice_model->get('id');
//       $f->model['client_id']  =  $invoice_model->get('bill_to_id');
//       $f->model->save();
//       return $app->jsRedirect($invoice->paymentPage->getURL());
//    });
//
//    // setup payment grid display
//    $crumb = $page->add(['BreadCrumb',null, 'big']);
//    $page->add(['ui' =>'divider']);
//
//    $crumb->addCrumb('Invoices', $invoice->getUrl());
//    $crumb->addCrumb($invoice_model->getTitle().' \'s payments');
//    $crumb->popTitle();
//
//    $m = $page->add('Menu');
//    $m->addItem(['Add Payment', 'icon' => 'plus'])->link($paymentEdit->getURL());
//    $m->addItem(['Edit Invoice', 'icon' => 'edit'])->link($invoice->invoicePage->getURL());
//
//    $gl = $page->add(['GridLayout', ['columns'=>3, 'rows'=>1]]);
//    $seg = $gl->add(['View', 'ui' => 'basic segment'], 'r1c1');
//    $card = $seg->add(['Card', 'header' => false]);
//    $card->setModel($invoice_model, ['client','balance', 'paid_total','g_total']);
//
//    $page->add(['ui' =>'hidden divider']);
//
//    // Add payment table.
//    $g = $page->add('Table');
//    $g->setModel($payment_model);
//    $actions = $g->addColumn(null, 'Actions');
//    $actions->addAction(['icon' => 'edit'], $invoice->jsIIF($paymentEdit->getURL(), 'pId'));
//    $actions->addAction(['icon' => 'trash'], function ($js, $id) use ($g, $seg){
//        $g->model->load($id)->delete();
//        return [$js->closest('tr')->transition('fade left'), $seg->jsReload()];
//    }, $invoice->confirmMsg);
//
//});
