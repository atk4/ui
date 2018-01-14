<?php
/**
 * Demonstrates how to use a wizard.
 */
require 'init.php';

$t = $app->add('Wizard');

// First step will automatcally be active when you open page first. It
// will contain the 'Next' button with a link.
$t->addStep('Welcome', function ($p) {
    $p->add(['Message', 'Welcome to wizard demonstration'])->text
        ->addParagraph('Use button "Next" to advance')
        ->addParagraph('You can specify your existing database connection string which will be used
        to create a table for model of your choice');
});

// If you add a form on a step, then wizard will automatically submit that
// form on "Next" button click, performing validation and submission. You do not need
// to return any action from form's onSubmit callback. You may also use memorize()
// to store wizard-specific variables
$t->addStep(['Set DSN', 'icon'=>'configure', 'description'=>'Database Connection String'], function ($p) {
    $f = $p->add('Form');

    $f->addField('dsn', 'Connect DSN', ['required'=>true])->placholder = 'mysql://user:pass@db-host.example.com/mydb';
    $f->onSubmit(function ($f) use ($p) {
        $p->memorize('dsn', $f->model['dsn']);

        return $p->jsNext();
    });
});

// Alternatvely, you may access buttonNext , buttonPrev properties of a wizard
// and set a custom js action or even set a different link. You can use recall()
// to access some values that were recorded on another steps.
$t->addStep(['Select Model', 'description'=>'"Country" or "Stat"', 'icon'=>'table'], function ($p) {
    if (isset($_GET['name'])) {
        $p->memorize('model', $_GET['name']);
        header('Location: '.$p->urlNext());
        exit;
    }


    $c = $p->add('Columns');

    $t = $c->addColumn()->add(['Grid', 'paginator'=>false, 'menu'=>false]);
    $c->addColumn()->add(['Message', 'Information', 'info'])->text
        ->addParagraph('Selecting which model you would like to import into your DSN. If corresponding table already exist, we might add extra fields into it. No tables, columns or rows will be deleted.');

    $t->setSource(['Country', 'Stat']);

    // should work after url() fix
    //$t->addDecorator('name', ['Link', [], ['name']]);

    $t->addDecorator('name', ['Link', [$p->stepCallback->name=>$p->currentStep], ['name']]);

    $p->buttonNext->addClass('disabled');
});

// Steps may contain interractive elements. You can disable navigational buttons
// and enable them as you see fit. Use handy js method to trigger advancement to
// the next step.
$t->addStep(['Migration', 'description'=>'Create or update table', 'icon'=>'database'], function ($p) {
    $c = $p->add('Console');

    $c->set(function ($c) use ($p) {
        $c->output('please wait');
        sleep(1);
        $c->output('DO IT!');

        return $p->jsNext();
    });
});

// calling addFinish adds a step, which will not appear in the list of steps, but
// will be displayed when you click the "Finish". Finish will not add any buttons
// because you shouldn't be able to navigate wizard back without restarting it.
// Only one finish can be added.
$t->addFinish(function ($p) {
    $c->add(['Header', 'You are DONE', 'huge centered']);
});
