<?php

declare(strict_types=1);

namespace Atk4\Ui\Repro;

use Atk4\Ui\Tests\DemosHttpTest;

require_once __DIR__ . '/vendor/autoload.php';

class Repro
{
    protected function getResponseFromRequest(string $path, ?array $post = []): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:9687' . $path);
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
        DemosHttpTest::setUpBeforeClass();
        $tc = new DemosHttpTest('x');
        \Closure::bind(static fn () => $tc->setUp(), null, DemosHttpTest::class)();
    }

    public function test(): void
    {
        [$code, $result] = $this->getResponseFromRequest('/demos/_unit-test/fatal-error.php?type=oom');

        var_dump($code);
        var_dump($result);
    }
}

$tc = new Repro();
$tc->setupWebserver();
$tc->test();
