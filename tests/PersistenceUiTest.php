<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Field;
use Atk4\Ui\Persistence\Ui as UiPersistence;

class PersistenceUiTest extends TestCase
{
    /**
     * @param mixed $phpValue
     * @param mixed $expectedUiValue
     *
     * @dataProvider providerTypecast
     */
    public function testTypecast(array $persistenceSeed, array $fieldSeed, $phpValue, $expectedUiValue): void
    {
        $p = (new UiPersistence())->setDefaults($persistenceSeed);
        $field = (new Field())->setDefaults($fieldSeed);

        if (is_string($phpValue) && str_starts_with($phpValue, '$ ')) {
            $phpValue = eval('return ' . substr($phpValue, 2) . ';');
        }

        $uiValue = $p->typecastSaveField($field, $phpValue);
        $this->assertSame($expectedUiValue, $uiValue);
        $readPhpValue = $p->typecastLoadField($field, $uiValue);
        if ($readPhpValue instanceof \DateTimeInterface) {
            $this->assertEquals($phpValue, $readPhpValue);
        } else {
            $this->assertSame($phpValue, $readPhpValue);
        }
        $uiValue = $p->typecastSaveField($field, $readPhpValue);
        $this->assertSame($expectedUiValue, $uiValue);
    }

    public function providerTypecast(): iterable
    {
        yield [[], [], '1', '1'];
        yield [[], ['type' => 'string'], '1', '1'];
        yield [[], ['type' => 'integer'], 1, '1'];
        yield [[], ['type' => 'float'], 1.1001, '1.1001'];
        yield [[], ['type' => 'boolean'], false, 'No'];
        yield [[], ['type' => 'boolean'], true, 'Yes'];

        foreach (['UTC', 'Europe/Prague', 'Pacific/Honolulu', 'Australia/Sydney'] as $tz) {
            $defaultTz = (new \DateTime())->getTimeZone()->getName();
            $evalDate = '$ new DateTime(\'2022-1-2 00:00\', new DateTimeZone(\'' . $defaultTz . '\'))';
            $evalTime = '$ new DateTime(\'1970-1-1 10:20\', new DateTimeZone(\'' . $defaultTz . '\'))';
            $evalDatetime = '$ new DateTime(\'2022-1-2 10:20:30\', new DateTimeZone(\'' . $tz . '\'))';

            yield [['timezone' => $tz], ['type' => 'date'], $evalDate, 'Jan 02, 2022'];
            yield [['timezone' => $tz], ['type' => 'time'], $evalTime, '10:20'];
            yield [['timezone' => $tz], ['type' => 'datetime'], $evalDatetime, 'Jan 02, 2022 10:20:30'];
            yield [['timezone' => $tz, 'date_format' => 'j.n.Y'], ['type' => 'date'], $evalDate, '2.1.2022'];
            yield [['timezone' => $tz, 'time_format' => 'g:i:s A'], ['type' => 'time'], $evalTime, '10:20:00 AM'];
            yield [['timezone' => $tz, 'datetime_format' => 'j.n.Y g:i:s A'], ['type' => 'datetime'], $evalDatetime, '2.1.2022 10:20:30 AM'];
        }
    }
}
