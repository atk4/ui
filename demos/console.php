<?php

require 'init.php';

class Test extends \atk4\data\Model
{
    use \atk4\core\DebugTrait;

    public function generateReport()
    {
        $this->log('info', 'Starting long process');
        $this->debug('test=123');
        sleep(1);
        $this->debug('test=321');
        sleep(5);

        return 123;
    }
}

$app->add('Console')->set(function ($console) {
    $console->output('Executing test process...');
    sleep(1);
    $console->output('Now trying something dangerous..');
    sleep(1);

    throw new \atk4\data\Exception('BOOM');
    $console->output('hello there');

    //$console->runCommand('ls', ['/etc']);

    //$console->send(new \atk4\ui\jsExpression('alert(1)'));
});

$app->add('Console')->setModel($app->add(new Test()), 'generateReport');
