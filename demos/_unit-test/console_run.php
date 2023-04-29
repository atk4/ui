<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\DebugTrait;
use Atk4\Ui\Console;
use Atk4\Ui\JsSse;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$testRunClass = AnonymousClassNameCache::get_class(fn () => new class() extends View {
    use DebugTrait;

    public function test(): int
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

$sse = JsSse::addTo($app);
$sse->setUrlTrigger('console_test');

$console = Console::addTo($app, ['sse' => $sse]);
$console->runMethod($testRunClass::addTo($app), 'test');
