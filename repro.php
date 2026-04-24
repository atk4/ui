<?php

declare(strict_types=1);

namespace Atk4\Ui\Repro;

use Atk4\Ui\Tests\DemosHttpTest;

require_once __DIR__ . '/vendor/autoload.php';

class Repro
{
    private DemosHttpTest $tc;

    public function setupWebserver(): void
    {
        DemosHttpTest::setUpBeforeClass();
        $this->tc = new DemosHttpTest('x');
        $tc = $this->tc;
        \Closure::bind(static fn () => $tc->setUp(), null, DemosHttpTest::class)();
    }

    public function curl(string $path, ?array $post = null): array
    {
        return $this->tc->curl('http://localhost:' . $this->port . '/' . $path, $post);
    }

    public function test(): void
    {
        [$code, $result] = $this->curl('demos/_unit-test/fatal-error.php?type=oom');

        var_dump($code);
        var_dump($result);
    }
}

$tc = new Repro();
$tc->setupWebserver();
$tc->test();
