<?php
/**
 * Demonstrates how to use tabs.
 */
require 'init.php';

$t = $app->add('Wizard');

// First step will automatcally be active when you open page first. It
// will contain the 'Next' button with a link.
$t->addStep('Welcome', function ($p) {
    $p->add(['Message', 'Nothing on the first page, so just proceed forward']);
});

// If you add a form on a step, then wizard will automatically submit that
// form on "Next" button click, performing validation and submission. You do not need
// to return any action from form's onSubmit callback. You may also use memorize()
// to store wizard-specific variables
$t->addStep('Set DNS', function ($p) {
    $f = $p->add('Form');

    $f->addField('dsn', 'Connect DSN', ['required'=>true])->placholder = 'mysql://user:pass@db-host.example.com/mydb';
    $f->onSubmit(function ($f) use ($p) {
        $p->memorize('dsn', $f->model['dsn']);
    });
});

// Alternatvely, you may access buttonNext , buttonPrev properties of a wizard
// and set a custom js action or even set a different link. You can use recall()
// to access some values that were recorded on another steps.
$t->addStep('Select Models', function ($p) {
    $t = $p->add('Grid');
    $t->setSource(['Country', 'Stat']);

    $sel = $g->addSelection();

    $p->buttonNext->on('click', function ($sel) {
        var_dump($sel);
    }, [$sel->jsChecked()]);
});

// Steps may contain interractive elements. You can disable navigational buttons
// and enable them as you see fit. Use handy js method to trigger advancement to
// the next step.
$t->addStep('Migration', function ($p) {
    $c = $p->add('Console');

    $c->set(function ($c) {
        $c->output('DO IT!');
    });
});

// calling addFinish adds a step, which will not appear in the list of steps, but
// will be displayed when you click "Finish". Finish step does not contain any
// buttons or navigational steps.
$t->addFinish(function ($p) {
    $c->add(['Header', 'You are DONE', 'huge centered']);
});
