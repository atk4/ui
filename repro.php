<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Symfony\Component\Process\Process;

require_once __DIR__ . '/vendor/autoload.php';

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

    protected function getResponseFromRequest(string $path): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://' . $this->host . ':' . $this->port . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_POST, 1);

//        $headers = [];
//        $headers[] = 'Authorization: Bearer ' . $token;
//        $headers[] = 'Dropbox-API-Arg: {"path":"' . $in_filepath . '"}';
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, \CURLINFO_HTTP_CODE);

        if ($result === false) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        return [$code, $result];
    }

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
                $this->getResponseFromRequest('/demos/?ping');

                break;
            } catch (\Exception $e) {
                if (microtime(true) - $ts > 5) {
                    throw $e;
                }
            }
        }
    }

    public function test(): void
    {
        [$code, $result] = $this->getResponseFromRequest('/demos/_unit-test/fatal-error.php?type=oom');

        var_dump($code);
        var_dump($result);
    }
}

$tc = new DemosHttpTest();
$tc->setupWebserver();
$tc->test();
