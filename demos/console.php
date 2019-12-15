<?php

require 'init.php';

if (!class_exists('TestConsole')) {
    class TestConsole extends \atk4\data\Model
    {
        use \atk4\core\DebugTrait;

        public function generateReport()
        {
            $this->log('info', 'Console will automatically pick up output from all DebugTrait objects');
            $this->debug('debug {foo}', ['foo' => 'bar']);
            $this->emergency('emergency {foo}', ['foo' => 'bar']);
            $this->alert('alert {foo}', ['foo' => 'bar']);
            $this->critical('critical {foo}', ['foo' => 'bar']);
            $this->error('error {foo}', ['foo' => 'bar']);
            $this->warning('warning {foo}', ['foo' => 'bar']);
            $this->notice('notice {foo}', ['foo' => 'bar']);
            $this->info('info {foo}', ['foo' => 'bar']);

            return 123;
        }
    }
}

$tt = $app->add('Tabs');

$t = $tt->addTab('set()');
$t->add([
    'Header',
    'icon'=> 'terminal',
    'Console output streaming',
    'subHeader'=> 'any output your PHP script produces through console is displayed to user in real-time',
]);
$t->add('Console')->set(function ($console) {
    $console->output('Executing test process...');
    sleep(1);
    $console->output('Now trying something dangerous..');
    sleep(1);
    echo 'direct output is captured';

    throw new \atk4\core\Exception('BOOM - exceptions are caught');
});

$t = $tt->addTab('runMethod()', function ($t) {
    $t->add([
        'Header',
        'icon'=> 'terminal',
        'Non-interractive method invocation',
        'subHeader'=> 'console can invoke a method, which normaly would be non-interractive and can still capture debug output',
    ]);
    $t->add('Console')->runMethod($t->add(new TestConsole()), 'generateReport');
});

$t = $tt->addTab('exec() single', function ($t) {
    $t->add([
        'Header',
        'icon'=> 'terminal',
        'Command execution',
        'subHeader'=> 'it is easy to run server-side commands and stream output through console',
    ]);
    $w = $t->add(['Message', 'This demo may not work', 'warning']);
    $w->text->addParagraph('This demo requires Linux OS and will display error otherwise.');
    $t->add('Console')->exec('/bin/pwd');
});

$t = $tt->addTab('exec() chain', function ($t) {
    $t->add([
        'Header',
        'icon'=> 'terminal',
        'Command execution',
        'subHeader'=> 'it is easy to run server-side commands and stream output through console',
    ]);
    $w = $t->add(['Message', 'This demo may not work', 'warning']);
    $w->text->addParagraph('This demo requires Linux OS and will display error otherwise.');
    $t->add('Console')->set(function ($c) {
        $c->exec('/sbin/ping', ['-c', '5', '-i', '1', '192.168.0.1']);
        $c->exec('/sbin/ping', ['-c', '5', '-i', '2', '8.8.8.8']);
        $c->exec('/bin/no-such-command');
    });
});

$t = $tt->addTab('composer update', function ($t) {
    $t->add([
        'Header',
        'icon'=> 'terminal',
        'Command execution',
        'subHeader'=> 'it is easy to run server-side commands and stream output through console',
    ]);

    $w = $t->add(['Message', 'This demo may not work', 'warning']);
    $w->text->addParagraph('This demo requires you to have "bash" and "composer" installed and may display error if the process running PHP does not have write access to the "vendor" folder and "composer.*".');

    $b = $w->add(['Button', 'I understand, proceed anyway', 'primary big']);

    $c = $t->add(['Console', 'event'=>false]);
    $c->exec('bash', ['-c', 'cd ..; echo "Running \'composer update\' in `pwd`"; composer --no-ansi update; echo "Self-updated. OK to refresh now!"']);

    $b->on('click', $c->jsExecute());
});

$t = $tt->addTab('Use after form submit', function ($t) {
    $t->add([
        'Header',
        'icon' => 'terminal',
        'How to log form submit process',
        'subHeader'=> 'Sometimes you can have long running process after form submit and want to show progress for user...',
    ]);

    session_start();

    $f = $t->add('Form');
    $f->addFields(['foo', 'bar']);

    $c = $t->add(['Console', 'event'=>false]);
    $c->set(function ($c) {
        $m = $_SESSION['data'];
        $c->output('Executing process...');
        $c->info(var_export($m->get(), true));
        sleep(1);
        $c->output('Wait...');
        sleep(3);
        $c->output('Process finished');
    });
    $c->js(true)->hide();

    $f->onSubmit(function ($ff) use ($c) {
        $_SESSION['data'] = $ff->model; // only option is to store model in session here in demo
        return [
            $c->js()->show(),
            $c->jsExecute(),
        ];
    });
});
