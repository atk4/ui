<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests\Table\Column\FilterModel;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Field;
use Atk4\Ui\Persistence\Ui as UiPersistence;
use Atk4\Ui\Table\Column\FilterModel\TypeDate;
use Atk4\Ui\Tests\CreateAppTrait;
use Atk4\Ui\Tests\PersistenceUiTest;
use PHPUnit\Framework\Attributes\DataProvider;

class AbstractTypeDatetimeTest extends TestCase
{
    use CreateAppTrait;

    /**
     * @dataProvider provideGetDatetimeCases
     */
    #[DataProvider('provideGetDatetimeCases')]
    public function testGetDatetime(string $timezone, string $modifier): void
    {
        $app = $this->createApp(['uiPersistence' => new UiPersistence(['timezone' => $timezone])]);
        $tableFilter = new TypeDate($app, ['lookupField' => new Field(['shortName' => 'foo'])]);

        $expectedDt = new \DateTime($modifier, new \DateTimeZone($timezone));

        $actualDt = $tableFilter->getDatetime($modifier);
        self::assertSame($expectedDt->getTimezone()->getName(), $actualDt->getTimezone()->getName());
        self::assertLessThan(3, $actualDt->getTimestamp() - $expectedDt->getTimestamp());
        self::assertGreaterThan(-3, $actualDt->getTimestamp() - $expectedDt->getTimestamp());
    }

    /**
     * @return iterable<list<mixed>>
     */
    public static function provideGetDatetimeCases(): iterable
    {
        foreach (PersistenceUiTest::provideGetTimezoneCases() as [$tz]) {
            yield [$tz, 'today'];
            yield [$tz, 'tomorrow'];
            yield [$tz, '-2 weeks'];
        }
    }
}
