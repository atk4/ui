<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests\Table\Column\FilterModel;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Field;
use Atk4\Ui\Persistence\Ui as UiPersistence;
use Atk4\Ui\Table\Column\FilterModel\TypeDate;
use Atk4\Ui\Table\Column\FilterModel\TypeDatetime;
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

        $expectedDt = new \DateTime($modifier, new \DateTimeZone($timezone));

        $assertSameDtFx = static function (\DateTime $v) use ($expectedDt) {
            self::assertSame($expectedDt->getTimezone()->getName(), $v->getTimezone()->getName());
            self::assertLessThan(3, $v->getTimestamp() - $expectedDt->getTimestamp());
            self::assertGreaterThan(-3, $v->getTimestamp() - $expectedDt->getTimestamp());
        };

        $tableFilter = new TypeDate($app, ['lookupField' => new Field(['shortName' => 'foo'])]);
        $assertSameDtFx($tableFilter->getDatetime($modifier));

        $tableFilter2 = new TypeDateTime($app, ['lookupField' => new Field(['shortName' => 'foo'])]);
        $assertSameDtFx($tableFilter2->getDatetime($modifier));
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
