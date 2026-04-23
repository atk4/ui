<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;

/** @var App $app */
require_once __DIR__ . '/demos/init-app.php';

use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\Process\Process;

class DemosHttpTest
{
    protected const ROOT_DIR = __DIR__;
    protected const DEMOS_DIR = self::ROOT_DIR . '/demos';

    /** @var Process|null */
    private static $_process;
    /** @var string|null */
    private static $_processSessionDir;

    /** @var string */
    protected $host = '127.0.0.1';
    /** @var int */
    protected $port = 9687;

    public function setupWebserver(): void
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
            } catch (ConnectException $e) {
                if (microtime(true) - $ts > 5) {
                    throw $e;
                }
            }
        }
    }
}

$tc = new DemosHttpTest();
$tc->setupWebserver();
