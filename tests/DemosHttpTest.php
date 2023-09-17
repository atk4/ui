<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Ui\Callback;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\LazyOpenStream;
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

    /** @var bool set the app->callExit in demo */
    protected $appCallExit = true;

    /** @var bool set the app->catchExceptions in demo */
    protected $appCatchExceptions = true;

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

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

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
        if (ini_get('open_basedir') !== '') {
            $cmdArgs[] = '-d';
            $cmdArgs[] = 'open_basedir=' . ini_get('open_basedir');
        }
        self::$_process = Process::fromShellCommandline('php ' . implode(' ', array_map('escapeshellarg', $cmdArgs)));
        self::$_process->disableOutput();
        self::$_process->start();

        // wait until server is ready
        $ts = microtime(true);
        while (true) {
            usleep(20_000);
            try {
                $this->getResponseFromRequest('?ping');

                break;
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                if (microtime(true) - $ts > 5) {
                    throw $e;
                }
            }
        }
    }

    protected function getClient(): Client
    {
        // never buffer the response thru disk, remove once streaming with curl is supported
        // https://github.com/guzzle/guzzle/issues/3115
        $sink = new LazyOpenStream('php://memory', 'w+');

        return new Client(['base_uri' => 'http://localhost:' . $this->port, 'sink' => $sink]);
    }

    protected function getPathWithAppVars(string $path): string
    {
        $path .= (!str_contains($path, '?') ? '?' : '&')
            . 'APP_CALL_EXIT=' . ((int) $this->appCallExit) . '&APP_CATCH_EXCEPTIONS=' . ((int) $this->appCatchExceptions);

        return parent::getPathWithAppVars($path);
    }

    /**
     * @dataProvider provideDemoLateOutputErrorCases
     */
    public function testDemoLateOutputError(string $urlTrigger, string $expectedOutput): void
    {
        $path = '_unit-test/late-output-error.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . $urlTrigger . '=ajax&'
            . Callback::URL_QUERY_TARGET . '=' . $urlTrigger . '&__atk_json=1';

        $response = $this->getResponseFromRequest5xx($path);

        self::assertSame(500, $response->getStatusCode());
        self::assertSame($expectedOutput, $response->getBody()->getContents());
    }

    public function provideDemoLateOutputErrorCases(): iterable
    {
        $hOutput = "\n" . '!! FATAL UI ERROR: Headers already sent, more headers cannot be set at this stage !!' . "\n";
        $oOutput = 'unmanaged output' . "\n" . '!! FATAL UI ERROR: Unexpected output detected !!' . "\n";

        yield ['err_headers_already_sent_2', $hOutput];
        yield ['err_unexpected_output_detected_2', $oOutput];
        yield ['err_headers_already_sent_1', $hOutput];
        yield ['err_unexpected_output_detected_1', $oOutput];
    }
}
