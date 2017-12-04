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

$app->add('Console')->setModel(new Test(), 'generateReport');

exit;
$app->add(['ui' => 'divider']);

$bar = $app->add(['View', 'template' => new \atk4\ui\Template('<div id="{$_id}" class="ui teal progress">
  <div class="bar"></div>
  <div class="label">Testing SSE</div>
</div>')]);
$bar->js(true)->progress();

$button = $app->add(['Button', 'Turn On']);
// non-SSE way
//$button->on('click', $bar->js()->progress(['percent'=> 40]));

$sse = $app->add(['jsSSE', 'showLoader' => true]);

$button->on('click', $sse->set(function () use ($sse, $bar) {
    $sse->send($bar->js()->progress(['percent' => 20]));
    sleep(0.5);
    $sse->send($bar->js()->progress(['percent' => 40]));
    sleep(1);
    $sse->send($bar->js()->progress(['percent' => 60]));
    sleep(2);
    $sse->send($bar->js()->progress(['percent' => 80]));
    sleep(1);

    // non-SSE way
    return $bar->js()->progress(['percent' => 100]);
}));
