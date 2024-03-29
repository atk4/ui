<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector as DriverSelector;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report;

class CoverageUtil
{
    private static ?CodeCoverage $coverage = null;

    private function __construct()
    {
        // zeroton
    }

    public static function start(Filter $filter): void
    {
        if (self::$coverage !== null) {
            throw new \Error('Coverage already started');
        }

        self::$coverage = new CodeCoverage((new DriverSelector())->forLineCoverage($filter), $filter);
        self::$coverage->cacheStaticAnalysis(sys_get_temp_dir() . '/phpunit-coverage.' . md5(__DIR__) . '.cache');
        self::$coverage->start(self::class);
    }

    /**
     * @return list<string>
     */
    private static function listFiles(string $directory): array
    {
        $res = [];
        foreach (array_diff(scandir($directory), ['.', '..']) as $v) {
            $path = $directory . '/' . $v;
            if (is_dir($path)) {
                foreach (self::listFiles($path) as $path2) {
                    $res[] = $path2;
                }
            } else {
                $res[] = $path;
            }
        }

        return $res;
    }

    public static function startFromPhpunitConfig(string $phpunitConfigDir): void
    {
        $phpunitCoverageConfig = simplexml_load_file($phpunitConfigDir . '/phpunit.xml.dist')->source;

        $excludeFiles = [];
        foreach ($phpunitCoverageConfig->exclude->directory ?? [] as $path) {
            foreach (self::listFiles($phpunitConfigDir . '/' . $path) as $path2) {
                $excludeFiles[] = $path2;
            }
        }
        foreach ($phpunitCoverageConfig->exclude->file ?? [] as $path) {
            $excludeFiles[] = $phpunitConfigDir . '/' . $path;
        }

        $files = [];
        foreach ($phpunitCoverageConfig->include->directory ?? [] as $path) {
            foreach (self::listFiles($phpunitConfigDir . '/' . $path) as $path2) {
                $files[] = $path2;
            }
        }
        $files = array_diff($files, $excludeFiles);

        foreach ($phpunitCoverageConfig->include->file ?? [] as $path) {
            $files[] = $phpunitConfigDir . '/' . $path;
        }

        // https://github.com/sebastianbergmann/phpunit/blob/11.0.2/src/TextUI/Configuration/CodeCoverageFilterRegistry.php#L57
        $filter = new Filter();
        $filter->includeFiles($files);
        static::start($filter);

        // fix https://github.com/sebastianbergmann/php-code-coverage/issues/942
        // https://github.com/sebastianbergmann/php-code-coverage/pull/939
        foreach ($filter->files() as $path) {
            opcache_compile_file($path);
        }
    }

    public static function saveData(string $outputDir): void
    {
        $outputFile = $outputDir . '/' . basename($_SERVER['SCRIPT_NAME'] ?? 'unknown', '.php') . '-' . hash('sha256', microtime(true) . random_bytes(64)) . '.cov';

        self::$coverage->stop();
        $writer = new Report\PHP();
        $writer->process(self::$coverage, $outputFile);
        self::$coverage = null;
    }

    public static function isCalledFromPhpunit(): bool
    {
        foreach (debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS) as $frame) {
            if (is_a($frame['class'] ?? null, TestCase::class, true)) {
                return true;
            }
        }

        return false;
    }
}
