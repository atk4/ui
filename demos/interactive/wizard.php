<?php

require_once __DIR__ . '/../atk-init.php';

use atk4\ui\Wizard;

/**
 * Demonstrates how to use a wizard.
 */

$t = Wizard::addTo($app);

// First step will automatcally be active when you open page first. It
// will contain the 'Next' button with a link.
$t->addStep('Welcome', function (Wizard $w) {
    \atk4\ui\Message::addTo($w, ['Welcome to wizard demonstration'])->text
        ->addParagraph('Use button "Next" to advance')
        ->addParagraph('You can specify your existing database connection string which will be used
        to create a table for model of your choice');
});

// If you add a form on a step, then wizard will automatically submit that
// form on "Next" button click, performing validation and submission. You do not need
// to return any action from form's onSubmit callback. You may also use memorize()
// to store wizard-specific variables
$t->addStep(['Set DSN', 'icon'=>'configure', 'description'=>'Database Connection String'], function (Wizard $w) {
    $f = \atk4\ui\Form::addTo($w);

    $f->addField('dsn', 'Connect DSN', ['required'=>true])->placeholder = 'mysql://user:pass@db-host.example.com/mydb';
    $f->onSubmit(function ($f) use ($w) {
        $w->memorize('dsn', $f->model['dsn']);

        return $w->jsNext();
    });
});

// Alternatvely, you may access buttonNext , buttonPrev properties of a wizard
// and set a custom js action or even set a different link. You can use recall()
// to access some values that were recorded on another steps.
$t->addStep(['Select Model', 'description'=>'"Country" or "Stat"', 'icon'=>'table'], function (Wizard $w) {
    if (isset($_GET['name'])) {
        $w->memorize('model', $_GET['name']);
        $w->app->redirect($w->urlNext());
    }

    $c = \atk4\ui\Columns::addTo($w);

    $t = \atk4\ui\Grid::addTo($c->addColumn(), ['paginator'=>false, 'menu'=>false]);
    \atk4\ui\Message::addTo($c->addColumn(), ['Information', 'info'])->text
        ->addParagraph('Selecting which model you would like to import into your DSN. If corresponding table already exist, we might add extra fields into it. No tables, columns or rows will be deleted.');

    $t->setSource(['Country', 'Stat']);

    // should work after url() fix
    $t->addDecorator('name', ['Link', [], ['name']]);

    //$t->addDecorator('name', ['Link', [$w->stepCallback->name=>$w->currentStep], ['name']]);

    $w->buttonNext->addClass('disabled');
});

// Steps may contain interractive elements. You can disable navigational buttons
// and enable them as you see fit. Use handy js method to trigger advancement to
// the next step.
$t->addStep(['Migration', 'description'=>'Create or update table', 'icon'=>'database'], function (Wizard $w) {
    $c = \atk4\ui\Console::addTo($w);
    $w->buttonFinish->addClass('disabled');

    $c->set(function ($c) use ($w) {
        $dsn = $w->recall('dsn');
        $model = $w->recall('model');

        $c->output('please wait');
        sleep(1);
        $c->output('connecting to "' . $dsn . '" (well not really, this is only a demo)');
        sleep(2);
        $c->output('initializing table for model "' . $model . '" (again - tricking you)');
        sleep(1);
        $c->output('DONE');

        $c->send($w->buttonFinish->js()->removeClass('disabled'));
    });
});

// calling addFinish adds a step, which will not appear in the list of steps, but
// will be displayed when you click the "Finish". Finish will not add any buttons
// because you shouldn't be able to navigate wizard back without restarting it.
// Only one finish can be added.
$t->addFinish(function (Wizard $w) {
    \atk4\ui\Header::addTo($w, ['You are DONE', 'huge centered']);
});
