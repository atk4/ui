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
     *
     * @dataProvider providerTypecastBidirectional
     * @dataProvider providerTypecastLoadOnly
     */
    public function testTypecast(array $persistenceSeed, array $fieldSeed, $phpValue, ?string $uiValue, bool $isUiValueNormalized = true): void
    {
        $p = (new UiPersistence())->setDefaults($persistenceSeed);
        $field = (new Field())->setDefaults($fieldSeed);

        if (is_string($phpValue) && preg_match('~^\$ new DateTime\(\'(.+)\'\)$~s', $phpValue, $matches)) {
            $phpValue = new \DateTime($matches[1]);
        }

        if ($isUiValueNormalized) {
            $savedUiValue = $p->typecastSaveField($field, $phpValue);
            self::assertSame($uiValue, $savedUiValue);
        }

        $readPhpValue = $p->typecastLoadField($field, $uiValue);
        if ($readPhpValue instanceof \DateTimeInterface) {
            $this->{'assertEquals'}($phpValue, $readPhpValue);
        } else {
            self::assertSame($phpValue, $readPhpValue);
        }

        $savedUiValue = $p->typecastSaveField($field, $readPhpValue);
        if ($isUiValueNormalized) {
            self::assertSame($uiValue, $savedUiValue);
        } else {
            self::assertNotSame($uiValue, $savedUiValue);
            $this->testTypecast($persistenceSeed, $fieldSeed, $phpValue, $savedUiValue);
        }
    }

    public function providerTypecastBidirectional(): iterable
    {
        $fixSpaceToNbspFx = static fn (string $v) => str_replace(' ', "\u{00a0}", $v);

        yield [[], [], '1', '1'];
        yield [[], [], '0', '0'];
        yield [[], ['type' => 'string'], '1', '1'];
        yield [[], ['type' => 'string'], '0', '0'];
        yield [[], ['type' => 'text'], "\n0\n\n0", "\n0\n\n0"];
        yield [[], ['type' => 'string'], '', ''];
        yield [[], ['type' => 'text'], '', ''];
        yield [[], ['type' => 'string', 'nullable' => false], '', ''];
        yield [[], ['type' => 'string', 'required' => true], '', ''];
        yield [[], ['type' => 'text'], "\n0", "\n0"];
        yield [[], ['type' => 'text', 'nullable' => false], '', ''];
        yield [[], ['type' => 'integer'], 1, '1'];
        yield [[], ['type' => 'integer'], 0, '0'];
        yield [[], ['type' => 'integer'], -1_100_230_000_456_345_678, $fixSpaceToNbspFx('-1 100 230 000 456 345 678')];
        yield [['thousandsSeparator' => ''], ['type' => 'integer'], 12_345_678, '12345678'];
        yield [['thousandsSeparator' => ','], ['type' => 'integer'], 12_345_678, '12,345,678'];
        yield [['decimalSeparator' => ',', 'thousandsSeparator' => '.'], ['type' => 'integer'], 12_345_678, '12.345.678'];
        yield [[], ['type' => 'float'], 1.0, '1.0'];
        yield [[], ['type' => 'float'], 0.0, '0.0'];
        yield [[], ['type' => 'float'], -1_100_230_000.4567, $fixSpaceToNbspFx('-1 100 230 000.4567')];
        yield [[], ['type' => 'float'], 1.100123, '1.100123'];
        yield [[], ['type' => 'float'], 1.100123E-6, '1.100123E-6'];
        yield [[], ['type' => 'float'], 1.100123E+221, '1.100123E+221'];
        yield [[], ['type' => 'float'], -1.100123E-221, '-1.100123E-221'];
        yield [['decimalSeparator' => ','], ['type' => 'float'], 12_345_678.3579, $fixSpaceToNbspFx('12 345 678,3579')];
        yield [['thousandsSeparator' => ''], ['type' => 'float'], 12_345_678.3579, '12345678.3579'];
        yield [['thousandsSeparator' => ','], ['type' => 'float'], 12_345_678.3579, '12,345,678.3579'];
        yield [['decimalSeparator' => ',', 'thousandsSeparator' => '.'], ['type' => 'float'], 12_345_678.3579, '12.345.678,3579'];
        yield [['decimalSeparator' => ',', 'thousandsSeparator' => '.'], ['type' => 'float'], 123.456, '123,456'];
        yield [[], ['type' => 'boolean'], false, 'No'];
        yield [[], ['type' => 'boolean'], true, 'Yes'];

        foreach (['UTC', 'Europe/Prague', 'Pacific/Honolulu', 'Australia/Sydney'] as $tz) {
            $evalDate = '$ new DateTime(\'2022-1-2 UTC\')';
            $evalTime1 = '$ new DateTime(\'1970-1-1 10:20 UTC\')';
            $evalTime2 = '$ new DateTime(\'1970-1-1 10:20:30 UTC\')';
            $evalTime3 = '$ new DateTime(\'1970-1-1 10:20:30.135789 UTC\')';
            $evalDatetime1 = '$ new DateTime(\'2022-1-2 10:20 ' . $tz . '\')';
            $evalDatetime2 = '$ new DateTime(\'2022-1-2 10:20:35 ' . $tz . '\')';
            $evalDatetime3 = '$ new DateTime(\'2022-1-2 10:20:35.42 ' . $tz . '\')';

            yield [['timezone' => $tz], ['type' => 'date'], $evalDate, 'Jan 2, 2022'];
            yield [['timezone' => $tz], ['type' => 'time'], $evalTime1, '10:20'];
            yield [['timezone' => $tz], ['type' => 'time'], $evalTime2, '10:20:30'];
            yield [['timezone' => $tz], ['type' => 'time'], $evalTime3, '10:20:30.135789'];
            yield [['timezone' => $tz], ['type' => 'datetime'], $evalDatetime2, 'Jan 2, 2022 10:20:35'];
            yield [['timezone' => $tz, 'dateFormat' => 'j.n.Y'], ['type' => 'date'], $evalDate, '2.1.2022'];
            yield [['timezone' => $tz, 'timeFormat' => 'g:i:s A'], ['type' => 'time'], $evalTime1, '10:20:00 AM'];
            yield [['timezone' => $tz, 'timeFormat' => 'g:i:s A'], ['type' => 'time'], $evalTime2, '10:20:30 AM'];
            yield [['timezone' => $tz, 'timeFormat' => 'g:i:s A'], ['type' => 'time'], $evalTime3, '10:20:30.135789 AM'];
            yield [['timezone' => $tz, 'timeFormat' => 'g:i:s.u A'], ['type' => 'time'], $evalTime2, '10:20:30.000000 AM'];
            yield [['timezone' => $tz, 'timeFormat' => 'g:i:s.u A'], ['type' => 'time'], $evalTime3, '10:20:30.135789 AM'];
            yield [['timezone' => $tz, 'datetimeFormat' => 'j.n.Y g:i:s A'], ['type' => 'datetime'], $evalDatetime1, '2.1.2022 10:20:00 AM'];
            yield [['timezone' => $tz, 'datetimeFormat' => 'j.n.Y g:i:s A'], ['type' => 'datetime'], $evalDatetime2, '2.1.2022 10:20:35 AM'];
            yield [['timezone' => $tz, 'datetimeFormat' => 'j.n.Y g:i:s A'], ['type' => 'datetime'], $evalDatetime3, '2.1.2022 10:20:35.42 AM'];
            yield [['timezone' => $tz, 'datetimeFormat' => 'j.n.Y g:i:s.u A'], ['type' => 'datetime'], $evalDatetime2, '2.1.2022 10:20:35.000000 AM'];
            yield [['timezone' => $tz, 'datetimeFormat' => 'j.n.Y g:i:s.u A'], ['type' => 'datetime'], $evalDatetime3, '2.1.2022 10:20:35.420000 AM'];
        }

        yield [[], ['type' => 'atk4_money'], 1.0, $fixSpaceToNbspFx('€ 1.00')];
        yield [[], ['type' => 'atk4_money'], 0.0, $fixSpaceToNbspFx('€ 0.00')];
        yield [['currency' => ''], ['type' => 'atk4_money'], 1.0, '1.00'];
        yield [['currency' => '$'], ['type' => 'atk4_money'], 1.0, $fixSpaceToNbspFx('$ 1.00')];
        yield [[], ['type' => 'atk4_money'], 1.1023, $fixSpaceToNbspFx('€ 1.1023')];
        yield [['currencyDecimals' => 0], ['type' => 'atk4_money'], 1.0, $fixSpaceToNbspFx('€ 1')];
        yield [['currencyDecimals' => 1], ['type' => 'atk4_money'], 1.1, $fixSpaceToNbspFx('€ 1.1')];
        yield [['currencyDecimals' => 4], ['type' => 'atk4_money'], 1.102, $fixSpaceToNbspFx('€ 1.1020')];
        yield [[], ['type' => 'atk4_money'], 1_234_056_789.1, $fixSpaceToNbspFx('€ 1 234 056 789.10')];
        yield [[], ['type' => 'atk4_money'], 234_056_789.101, $fixSpaceToNbspFx('€ 234 056 789.101')];
        yield [['decimalSeparator' => ','], ['type' => 'atk4_money'], 1.0, $fixSpaceToNbspFx('€ 1,00')];
        yield [[], ['type' => 'atk4_money'], 12_345_678.3, $fixSpaceToNbspFx('€ 12 345 678.30')];
        yield [['decimalSeparator' => ','], ['type' => 'atk4_money'], 12_345_678.3, $fixSpaceToNbspFx('€ 12 345 678,30')];
        yield [['thousandsSeparator' => ''], ['type' => 'atk4_money'], 12_345_678.3, $fixSpaceToNbspFx('€ 12345678.30')];
        yield [['thousandsSeparator' => ','], ['type' => 'atk4_money'], 12_345_678.3, $fixSpaceToNbspFx('€ 12,345,678.30')];
        yield [['decimalSeparator' => ',', 'thousandsSeparator' => '.'], ['type' => 'atk4_money'], 12_345_678.3, $fixSpaceToNbspFx('€ 12.345.678,30')];

        foreach (['string', 'text', 'integer', 'float', 'boolean', 'date', 'time', 'datetime', 'atk4_money'] as $type) {
            yield [[], ['type' => $type], null, null];
        }
    }

    public function providerTypecastLoadOnly(): iterable
    {
        foreach (['integer', 'float', 'boolean', 'date', 'time', 'datetime', 'atk4_money'] as $type) {
            yield [[], ['type' => $type], null, '', false];
        }

        yield [[], ['type' => 'string'], '', ' ', false];
        yield [[], ['type' => 'string'], '', " \r\r\n ", false];
        yield [[], ['type' => 'string', 'nullable' => false], '', ' ', false];
        yield [[], ['type' => 'string', 'nullable' => false], '', " \n ", false];
        yield [[], ['type' => 'text'], "\n0", "\r0", false];
        yield [[], ['type' => 'text'], "\n0", "\r\n0", false];

        yield [[], ['type' => 'boolean'], false, '0', false];
        yield [[], ['type' => 'boolean'], true, '1', false];
        yield [[], ['type' => 'boolean'], false, ' 0' . "\n", false];

        yield [[], ['type' => 'integer'], 12_345_678, '12345678', false];
        yield [[], ['type' => 'integer'], 12_345_678, '12_345_678', false];
        yield [[], ['type' => 'integer'], 12_345_678, '12 345 678', false];
        yield [[], ['type' => 'integer'], 12_345_678, "\r" . '12345678', false];
        yield [[], ['type' => 'integer'], 628, '6_28', false];
        yield [[], ['type' => 'integer'], 628, '6 28', false];
        yield [['thousandsSeparator' => ','], ['type' => 'integer'], 628, '6,28', false];
        yield [[], ['type' => 'integer'], 0, '0.4', false];
        yield [[], ['type' => 'integer'], 7, '7.49', false];
        // yield [[], ['type' => 'integer'], 8, '7.5', false];
        yield [[], ['type' => 'integer'], -7, '-7.49', false];
        // yield [[], ['type' => 'integer'], -8, '-7.5', false];
        yield [[], ['type' => 'integer'], 12, '12.345.678', false];
        yield [[], ['type' => 'integer'], 123, '123,456', false];
        yield [['decimalSeparator' => ','], ['type' => 'integer'], 123, '123.456', false];

        yield [[], ['type' => 'float'], 1.0, '1', false];
        yield [[], ['type' => 'float'], 0.0, '0', false];
        yield [[], ['type' => 'float'], 0.3, '.3', false];
        yield [[], ['type' => 'float'], -0.3, '-.3', false];
        yield [[], ['type' => 'float'], 0.3, '+00.3', false];
        yield [[], ['type' => 'float'], -0.3, '-00.300', false];
        yield [[], ['type' => 'float'], 12_345_678.3579, '12345678.3579', false];
        yield [[], ['type' => 'float'], 12_345_678.3579, '12_345_678.357_9', false];
        yield [[], ['type' => 'float'], 12_345_678.3579, '12 345 678.357 9', false];
        yield [[], ['type' => 'float'], 123.456, '123,456', false];
        yield [['decimalSeparator' => ','], ['type' => 'float'], 123.456, '123.456', false];

        yield [[], ['type' => 'date'], '$ new DateTime(\'2022-1-2 UTC\')', 'Jan 02, 2022', false];

        yield [[], ['type' => 'atk4_money'], 2.0, '€2', false];
        yield [[], ['type' => 'atk4_money'], 2.0, '2€', false];
        yield [[], ['type' => 'atk4_money'], -1.3, '€-1.3', false];
        yield [[], ['type' => 'atk4_money'], -1.3, '-€1.3', false];
        yield [[], ['type' => 'atk4_money'], -1.3, '-1.3€', false];
        yield [[], ['type' => 'atk4_money'], 0.3, '€.3', false];
        yield [[], ['type' => 'atk4_money'], 0.3, '.3€', false];
        yield [[], ['type' => 'atk4_money'], -1.3, '-1€3', false];
        yield [['currency' => 'USD'], ['type' => 'atk4_money'], -1.3, '-1 USD 3', false];
    }
}
