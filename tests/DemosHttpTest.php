<?php

declare(strict_types=1);

namespace atk4\ui\tests;

use GuzzleHttp\Client;
use Symfony\Component\Process\Process;

/**
 * Same as DemosTest, but using native HTTP to check if output and shutdown handlers work correctly.
 *
 * @group demos_http
 */
class DemosHttpTest extends DemosTest
{
    private static $_process;
    private static $_processSessionDir;

    /** @var bool set the app->call_exit in demo */
    protected static $app_def_call_exit = true;

    /** @var bool set the app->caught_exception in demo */
    protected static $app_def_caught_exception = true;

    protected static $host = '127.0.0.1';
    protected static $port = 9687;

    protected static $webserver_root = 'demos/';

    public static function tearDownAfterClass(): void
    {
        if (file_exists($file = self::getPackagePath('demos', 'coverage.php'))) {
            unlink($file);
        }

        // cleanup session storage
        foreach (scandir(self::$_processSessionDir) as $f) {
            if (!in_array($f, ['.', '..'], true)) {
                unlink(self::$_processSessionDir . '/' . $f);
            }
        }
        rmdir(self::$_processSessionDir);
    }

    public static function setUpBeforeClass(): void
    {
        if (extension_loaded('xdebug') || isset($this) && $this->getResult()->getCodeCoverage() !== null) { // dirty way to skip coverage for phpunit with disabled coverage
            $coverage = self::getPackagePath('coverage');
            if (!file_exists($coverage)) {
                mkdir($coverage, 0777, true);
            }

            $demosCoverage = self::getPackagePath('demos', 'coverage.php');
            if (!file_exists($demosCoverage)) {
                file_put_contents(
                    $demosCoverage,
                    file_get_contents(self::getPackagePath('tools', 'coverage.php'))
                );
            }
        }

        // spin up the test server
        if (\PHP_SAPI !== 'cli') {
            throw new \Error('Builtin web server can we started only from CLI'); // prevent to start a process if tests are not run from CLI
        }

        // setup session storage
        self::$_processSessionDir = sys_get_temp_dir() . '/atk4_test__ui__session';
        if (!file_exists(self::$_processSessionDir)) {
            mkdir(self::$_processSessionDir);
        }

        $cmdArgs = [
            '-S', static::$host . ':' . static::$port,
            '-t', self::getPackagePath(),
            '-d', 'session.save_path=' . self::$_processSessionDir,
        ];
        if (!empty(ini_get('open_basedir'))) {
            $cmdArgs[] = '-d';
            $cmdArgs[] = 'open_basedir=' . ini_get('open_basedir');
        }
        self::$_process = Process::fromShellCommandline('php ' . implode(' ', array_map('escapeshellarg', $cmdArgs)));

        // disabling the output, otherwise the process might hang after too much output
        self::$_process->disableOutput();

        // execute the command and start the process
        self::$_process->start();

        usleep(250 * 1000);
    }

    /**
     * TODO remove this or replace with better impl.
     */
    protected static function getPackagePath($directory = null, $_ = null): string
    {
        $route = func_get_args();
        $baseDir = realpath(__DIR__ . \DIRECTORY_SEPARATOR . '..');
        array_unshift($route, $baseDir);

        return implode(\DIRECTORY_SEPARATOR, $route);
    }

    protected function getClient(): Client
    {
        return new Client(['base_uri' => 'http://localhost:' . self::$port]);
    }

    protected function getPathWithAppVars(string $path): string
    {
        $path .= strpos($path, '?') === false ? '?' : '&';
        $path .= 'APP_CALL_EXIT=' . ((int) static::$app_def_call_exit) . '&APP_CATCH_EXCEPTIONS=' . ((int) static::$app_def_caught_exception);

        return 'demos/' . $path;
    }
}
