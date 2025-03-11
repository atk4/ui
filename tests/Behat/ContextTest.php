<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests\Behat;

use Atk4\Core\Phpunit\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ContextTest extends TestCase
{
    /**
     * @return array<string>
     */
    protected function extractPhpdocRegexes(string $file): array
    {
        $content = file_get_contents($file);

        $res = [];
        preg_match_all('~@(Given|Then|When) (.*)~', $content, $matchesAll, \PREG_SET_ORDER);
        foreach ($matchesAll as $matches) {
            $res[] = $matches[2];
        }

        return $res;
    }

    /**
     * @dataProvider provideFiles
     */
    #[DataProvider('provideFiles')]
    public function testFileHasRegexes(string $file): void
    {
        self::assertGreaterThan(0, count($this->extractPhpdocRegexes($file)));
    }

    /**
     * @dataProvider provideFiles
     */
    #[DataProvider('provideFiles')]
    public function testRegexStartAndEnd(string $file): void
    {
        foreach ($this->extractPhpdocRegexes($file) as $regex) {
            self::assertStringStartsWith('~^', $regex);
            self::assertStringEndsWith('$~', $regex);
            self::assertStringEndsNotWith('.$~', $regex);
        }
    }

    /**
     * @dataProvider provideFiles
     */
    #[DataProvider('provideFiles')]
    public function testRegexArgumentFormat(string $file): void
    {
        foreach ($this->extractPhpdocRegexes($file) as $regex) {
            preg_match_all('~\((?:\(.*?\)|.)+?\)~', $regex, $matchesAll, \PREG_SET_ORDER);
            foreach ($matchesAll as $matches) {
                if (!str_contains($matches[0], '"')) {
                    continue;
                }

                self::assertSame('("(?:\\\[\\\"]|[^"])*+")', $matches[0]);
            }
        }
    }

    /**
     * @return iterable<list<mixed>>
     */
    public static function provideFiles(): iterable
    {
        yield [dirname(__DIR__, 2) . '/src/Behat/Context.php'];
    }
}
