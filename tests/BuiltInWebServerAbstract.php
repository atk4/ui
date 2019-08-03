<?php

namespace atk4\ui\tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use GuzzleHttp\Client;

abstract class BuiltInWebServerAbstract extends TestCase
{
    protected static $process;

    const HOST = "127.0.0.1";
    const PORT = 9876; // Adjust this to a port you're sure is free

    protected static $webserver_root = 'demos';

    public static function setUpBeforeClass()
    {
        if (!file_exists(getcwd() . '/demos/coverage.php')) {
            file_put_contents(
                getcwd() . '/demos/coverage.php',
                file_get_contents(getcwd() . '/tools/coverage.php')
            );
        }

        // The command to spin up the server
        self::$process = new Process(['php --coverage-clover -d xdebug.remote_enable=1 -d xdebug.coverage_enable=1 -S ' . self::HOST . ':' . self::PORT . ' -t ' . getcwd() . DIRECTORY_SEPARATOR . self::$webserver_root]);

        // Disabling the output, otherwise the process might hang after too much output
        self::$process->disableOutput();
        // Actually execute the command and start the process

        self::$process->start();

        sleep(1);
    }

    public static function tearDownAfterClass()
    {
        self::$process->stop();

        if (file_exists(getcwd() . '/demos/coverage.php')) {
            unlink(getcwd() . '/demos/coverage.php');
        }
    }

    private function getClient(): Client
    {
        // Creating a Guzzle Client with the base_uri, so we can use a relative
        // path for the requests.
        return new Client(['base_uri' => 'http://127.0.0.1:' . self::PORT]);
    }

    protected function getResponseFromRequestFormPOST($path, $data): ResponseInterface
    {
        return $this->getClient()->request('POST', $path, ['form_params' => $data]);
    }

    protected function getResponseFromRequestGET($path): ResponseInterface
    {
        return $this->getClient()->request('GET', $path);
    }
}