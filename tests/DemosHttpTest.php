<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Ui\Callback;
use GuzzleHttp\Client;
use Symfony\Component\Process\Process;

/**
 * Same as DemosTest, but using native HTTP to check if output and shutdown handlers work correctly.
 *
 * @group demos_http
 */
class DemosHttpTest extends DemosTest
{
    /** @var Process|null */
    private static $_process;
    /** @var string|null */
    private static $_processSessionDir;

    /** @var bool set the app->call_exit in demo */
    protected $app_call_exit = true;

    /** @var bool set the app->catch_exceptions in demo */
    protected $app_catch_exceptions = true;

    /** @var string */
    protected $host = '127.0.0.1';
    /** @var int */
    protected $port = 9687;

    public static function tearDownAfterClass(): void
    {
        // stop the test server
        usleep(250_000);
        self::$_process->stop(1); // TODO we may need to add pcntl_async_signals/pcntl_signal to CoverageUtil.php
        self::$_process = null;

        // cleanup session storage
        foreach (scandir(self::$_processSessionDir) as $f) {
            if (!in_array($f, ['.', '..'], true)) {
                unlink(self::$_processSessionDir . '/' . $f);
            }
        }
        rmdir(self::$_processSessionDir);
        self::$_processSessionDir = null;
    }

    protected function setUp(): void
    {
        if (self::$_process === null) {
            if (\PHP_SAPI !== 'cli') {
                throw new \Error('Builtin webserver can be started only from CLI');
            }

            $this->setupWebserver();
        }
    }

    private function setupWebserver(): void
    {
        // setup session storage
        self::$_processSessionDir = sys_get_temp_dir() . '/atk4_test__ui__session';
        if (!file_exists(self::$_processSessionDir)) {
            mkdir(self::$_processSessionDir);
        }

        // spin up the test server
        $cmdArgs = [
            '-S', $this->host . ':' . $this->port,
            '-t', static::ROOT_DIR,
            '-d', 'session.save_path=' . self::$_processSessionDir,
        ];
        if (!empty(ini_get('open_basedir'))) {
            $cmdArgs[] = '-d';
            $cmdArgs[] = 'open_basedir=' . ini_get('open_basedir');
        }
        self::$_process = Process::fromShellCommandline('php ' . implode(' ', array_map('escapeshellarg', $cmdArgs)));
        self::$_process->disableOutput();
        self::$_process->start();

        // wait until server is ready
        $ts = microtime(true);
        while (microtime(true) - $ts < 5) {
            usleep(20_000);
            try {
                $this->getResponseFromRequest('?ping');

                break;
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
            }
        }
    }

    protected function getClient(): Client
    {
        return new Client(['base_uri' => 'http://localhost:' . $this->port]);
    }

    protected function getPathWithAppVars(string $path): string
    {
        $path .= strpos($path, '?') === false ? '?' : '&';
        $path .= 'APP_CALL_EXIT=' . ((int) $this->app_call_exit) . '&APP_CATCH_EXCEPTIONS=' . ((int) $this->app_catch_exceptions);

        return parent::getPathWithAppVars($path);
    }

    private function getLateOutputErrorPath(string $trigger): string
    {
        return '_unit-test/late-output-error.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . $trigger . '=ajax&'
            . Callback::URL_QUERY_TARGET . '=' . $trigger . '&__atk_json=1';
    }

    public function testDemoLateOutputErrorHeadersAlreadySent(): void
    {
        $response = $this->getResponseFromRequest5xx($this->getLateOutputErrorPath('err_headers_already_sent'));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame(
            "\n" . '!! FATAL UI ERROR: Headers already sent, more headers cannot be set at this stage !!' . "\n",
            $response->getBody()->getContents()
        );
    }

    public function testDemoLateOutputErrorUnexpectedOutputDetected(): void
    {
        $response = $this->getResponseFromRequest5xx($this->getLateOutputErrorPath('err_unexpected_output_detected'));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame(
            'unmanaged output'
                . "\n" . '!! FATAL UI ERROR: Unexpected output detected !!' . "\n",
            $response->getBody()->getContents()
        );
    }
}
