<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\jsSSE;

require_once __DIR__ . '/../atk-init.php';

$testRunClass = get_class(new class() extends \atk4\ui\View {
    use \atk4\core\DebugTrait;

    public function test()
    {
        $this->log('info', 'Console will automatically pick up output from all DebugTrait objects');
        $this->debug('debug');
        $this->emergency('emergency');
        $this->alert('alert');
        $this->critical('critical');
        $this->error('error');
        $this->warning('warning');
        $this->notice('notice');
        $this->info('info');

        return 123;
    }
});

$sse = jsSSE::addTo($app);
$sse->urlTrigger = 'console_test';

$console = \atk4\ui\Console::addTo($app, ['sse' => $sse]);
$console->runMethod($testRunClass::addTo($app), 'test');
