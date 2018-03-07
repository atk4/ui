<?php

require 'init.php';

class Test extends \atk4\data\Model
{
    use \atk4\core\DebugTrait;

    public function generateReport()
    {
        $this->log('info', 'Console will automatically pick up output from all DebugTrait objects');
        $this->debug('debug {foo}', ['foo'=>'bar']);
        $this->emergency('emergency {foo}', ['foo'=>'bar']);
        $this->alert('alert {foo}', ['foo'=>'bar']);
        $this->critical('critical {foo}', ['foo'=>'bar']);
        $this->error('error {foo}', ['foo'=>'bar']);
        $this->warning('warning {foo}', ['foo'=>'bar']);
        $this->notice('notice {foo}', ['foo'=>'bar']);
        $this->info('info {foo}', ['foo'=>'bar']);

        return 123;
    }
}

$tt = $app->add('Tabs');

$t = $tt->addTab('set()');
$t->add([
    'Header',
    'icon'=>'terminal',
    'Console output streaming',
    'subHeader'=>'any output your PHP script produces through console is displayed to user in real-time',
]);
$t->add('Console')->set(function ($console) {
    $console->output('Executing test process...');
    sleep(1);
    $console->output('Now trying something dangerous..');
    sleep(1);
    echo 'direct output is captured';

    throw new \atk4\data\Exception('BOOM - exceptions are caught');
});

$t = $tt->addTab('runMethod()', function ($t) {
    $t->add([
        'Header',
        'icon'=>'terminal',
        'Non-interractive method invocation',
        'subHeader'=>'console can invoke a method, which normaly would be non-interractive and can still capture debug output',
    ]);
    $t->add('Console')->runMethod($t->add(new Test()), 'generateReport');
});

$t = $tt->addTab('exec() single', function ($t) {
    $t->add([
        'Header',
        'icon'=>'terminal',
        'Command execution',
        'subHeader'=>'it is easy to run server-side commands and stream output through console',
    ]);
    $t->add('Console')->exec('/bin/pwd');
});

$t = $tt->addTab('exec() chain', function ($t) {
    $t->add([
        'Header',
        'icon'=>'terminal',
        'Command execution',
        'subHeader'=>'it is easy to run server-side commands and stream output through console',
    ]);
    $t->add('Console')->set(function ($c) {
        $c
            ->exec('/sbin/ping', ['-c', '5', '-i', '1', '192.168.0.1'])
            ->exec('/sbin/ping', ['-c', '5', '-i', '2', '8.8.8.8'])
            ->exec('/bin/no-such-command');
    });
});

$t = $tt->addTab('composer update', function ($t) {
    $t->add([
        'Header',
        'icon'=>'terminal',
        'Command execution',
        'subHeader'=>'it is easy to run server-side commands and stream output through console',
    ]);
    $t->add('Console')->exec('bash', ['-c', 'cd ..; echo "Running \'composer update\' in `pwd`"; composer --no-ansi update; echo "Self-updated. OK to refresh now!"']);
});
