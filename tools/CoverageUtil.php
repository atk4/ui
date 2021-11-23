<?php

declare(strict_types=1);

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector as DriverSelector;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report;

final class CoverageUtil
{
    /** @var CodeCoverage */
    private static $coverage;

    private function __construct()
    {
        // zeroton
    }

    public static function start(): void
    {
        if (self::$coverage !== null) {
            throw new \Error('Coverage already started');
        }

        $filter = new Filter();
        $filter->includeDirectory(__DIR__ . '/../src');
        self::$coverage = new CodeCoverage((new DriverSelector())->forLineCoverage($filter), $filter);
        self::$coverage->start($_SERVER['SCRIPT_NAME']);
    }

    public static function saveData(): void
    {
        self::$coverage->stop();
        $writer = new Report\PHP();
        $writer->process(self::$coverage, dirname(__DIR__) . '/coverage/' . basename($_SERVER['SCRIPT_NAME'], '.php') . '-' . hash('sha256', microtime(true) . random_bytes(64)) . '.cov');
    }
}
