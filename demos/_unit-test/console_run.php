<?php

namespace atk4\ui\demo;

use atk4\ui\jsSSE;

require_once __DIR__ . '/../atk-init.php';

$testConsoleClass = get_class(new class() extends \atk4\data\Model {
    use \atk4\core\DebugTrait;
    use \atk4\core\StaticAddToTrait;

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
});

$sse = jsSSE::addTo($app);
$sse->urlTrigger = 'console_test';

$console = \atk4\ui\Console::addTo($app, ['sse' => $sse]);
$console->runMethod($testConsoleClass::addTo($app), 'generateReport');
