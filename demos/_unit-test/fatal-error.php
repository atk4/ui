<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$runOnShutdownFx = static function (\Closure $fx) use ($app) {
    // relies on https://github.com/atk4/ui/blob/5.0.0/src/App.php#L1108
    $hookIndex = $app->onHook(App::HOOK_BEFORE_RENDER, static function () use ($app, &$hookIndex, $fx) {
        $app->removeHook(App::HOOK_BEFORE_RENDER, $hookIndex, true);

        $fx();
    });
};

$type = $app->tryGetRequestQueryParam('type');
if ($type === 'oom') {

    var_dump(ini_get('memory_limit'));
    var_dump('x');

    ini_set('memory_limit', '64M');

    $str = '';
    for ($i = 0; $i < 256; ++$i) {
        $str .= random_bytes(256 * 1024);
    }

    var_dump(memory_get_usage(true));
    var_dump(memory_get_usage(false));
    var_dump(ini_get('memory_limit'));
    var_dump('y');
} elseif ($type === 'time-limit') {
    set_time_limit(1);

    $str = '';
    $t = microtime(true);
    while (microtime(true) - $t < 1.5) {
        $str = md5($str);
    }
} elseif ($type === 'compile-error') {
    // E_COMPILE_ERROR
    // https://github.com/php/php-src/blob/php-8.3.0/Zend/zend_compile.c#L7459
    // https://github.com/php/php-src/issues/9333
    eval('class Cl { function foo(); }');
} elseif ($type === 'compile-warning') {
    // E_COMPILE_WARNING
    // https://github.com/php/php-src/blob/php-8.3.0/Zend/zend_compile.c#L6366
    eval('declare(x=1);');
} elseif ($type === 'exception-in-shutdown') {
    $runOnShutdownFx(static function () {
        throw new \Exception('Exception from shutdown');
    });
} elseif ($type === 'warning-in-shutdown') {
    $runOnShutdownFx(static function () {
        trigger_error('Warning from shutdown');
    });
}
