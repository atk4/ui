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
     * @param mixed $uiValue
     *
     * @dataProvider providerTypecastBidirectional
     * @dataProvider providerTypecastLoadOnly
     */
    public function testTypecast(array $persistenceSeed, array $fieldSeed, $phpValue, $uiValue, bool $isUiValueNormalized = true): void
    {
        $p = (new UiPersistence())->setDefaults($persistenceSeed);
        $field = (new Field())->setDefaults($fieldSeed);

        if (is_string($phpValue) && preg_match('~^\$ new DateTime\(\'(.+)\'\)$~s', $phpValue, $matches)) {
            $phpValue = new \DateTime($matches[1]);
        }

        if ($isUiValueNormalized) {
            $savedUiValue = $p->typecastSaveField($field, $phpValue);
            static::assertSame($uiValue, $savedUiValue);
        }

        $readPhpValue = $p->typecastLoadField($field, $uiValue);
        if ($readPhpValue instanceof \DateTimeInterface) {
            $this->{'assertEquals'}($phpValue, $readPhpValue);
        } else {
            static::assertSame($phpValue, $readPhpValue);
        }

        $savedUiValue = $p->typecastSaveField($field, $readPhpValue);
        if ($isUiValueNormalized) {
            static::assertSame($uiValue, $savedUiValue);
        } else {
            $this->testTypecast($persistenceSeed, $fieldSeed, $phpValue, $savedUiValue);
        }
    }

    public function providerTypecastBidirectional(): iterable
    {
        $fixSpaceToNbspFx = fn (string $v) => str_replace(' ', "\u{00a0}", $v);

        yield [[], [], '1', '1'];
        yield [[], [], '0', '0'];
        yield [[], ['type' => 'string'], '1', '1'];
        yield [[], ['type' => 'string'], '0', '0'];
        yield [[], ['type' => 'text'], "\n0\n\n0", "\n0\n\n0"];
        yield [[], ['type' => 'integer'], 1, '1'];
        yield [[], ['type' => 'integer'], 0, '0'];
        yield [[], ['type' => 'integer'], -1_100_230_000_456_345_678, '-1100230000456345678'];
        yield [[], ['type' => 'float'], 1.0, '1'];
        yield [[], ['type' => 'float'], 0.0, '0'];
        yield [[], ['type' => 'float'], -1_100_230_000.4567, '-1100230000.4567'];
        yield [[], ['type' => 'float'], 1.100123, '1.100123'];
        yield [[], ['type' => 'float'], 1.100123E-6, '1.100123E-6'];
        yield [[], ['type' => 'float'], 1.100123E+221, '1.100123E+221'];
        yield [[], ['type' => 'float'], -1.100123E-221, '-1.100123E-221'];
        yield [[], ['type' => 'boolean'], false, 'No'];
        yield [[], ['type' => 'boolean'], true, 'Yes'];

        foreach (['UTC', 'Europe/Prague', 'Pacific/Honolulu', 'Australia/Sydney'] as $tz) {
            $evalDate = '$ new DateTime(\'2022-1-2 UTC\')';
            $evalTime = '$ new DateTime(\'1970-1-1 10:20 UTC\')';
            $evalDatetime = '$ new DateTime(\'2022-1-2 10:20:30 ' . $tz . '\')';

            yield [['timezone' => $tz], ['type' => 'date'], $evalDate, 'Jan 02, 2022'];
            yield [['timezone' => $tz], ['type' => 'time'], $evalTime, '10:20'];
            yield [['timezone' => $tz], ['type' => 'datetime'], $evalDatetime, 'Jan 02, 2022 10:20:30'];
            yield [['timezone' => $tz, 'dateFormat' => 'j.n.Y'], ['type' => 'date'], $evalDate, '2.1.2022'];
            yield [['timezone' => $tz, 'timeFormat' => 'g:i:s A'], ['type' => 'time'], $evalTime, '10:20:00 AM'];
            yield [['timezone' => $tz, 'datetimeFormat' => 'j.n.Y g:i:s A'], ['type' => 'datetime'], $evalDatetime, '2.1.2022 10:20:30 AM'];
        }

        yield [[], ['type' => 'atk4_money'], 1.0, $fixSpaceToNbspFx('€ 1.00')];
        yield [[], ['type' => 'atk4_money'], 0.0, $fixSpaceToNbspFx('€ 0.00')];
        yield [['currency' => ''], ['type' => 'atk4_money'], 1.0, $fixSpaceToNbspFx('1.00')];
        yield [['currency' => '$'], ['type' => 'atk4_money'], 1.0, $fixSpaceToNbspFx('$ 1.00')];
        yield [[], ['type' => 'atk4_money'], 1.1023, $fixSpaceToNbspFx('€ 1.1023')];
        yield [['currencyDecimals' => 4], ['type' => 'atk4_money'], 1.102, $fixSpaceToNbspFx('€ 1.1020')];
        yield [[], ['type' => 'atk4_money'], 1_234_056_789.1, $fixSpaceToNbspFx('€ 1 234 056 789.10')];
        yield [[], ['type' => 'atk4_money'], 234_056_789.101, $fixSpaceToNbspFx('€ 234 056 789.101')];
        yield [['currencyDecimalSeparator' => ','], ['type' => 'atk4_money'], 1.0, $fixSpaceToNbspFx('€ 1,00')];
        yield [[], ['type' => 'atk4_money'], 1000.0, $fixSpaceToNbspFx('€ 1 000.00')];
        yield [['currencyThousandsSeparator' => ','], ['type' => 'atk4_money'], 1000.0, $fixSpaceToNbspFx('€ 1,000.00')];
        yield [['currencyDecimalSeparator' => ',', 'currencyThousandsSeparator' => '.'], ['type' => 'atk4_money'], 1000.0, $fixSpaceToNbspFx('€ 1.000,00')];

        foreach (['string', 'text', 'integer', 'float', 'boolean', 'date', 'time', 'datetime', 'atk4_money'] as $type) {
            yield [[], ['type' => $type], null, null];
        }
    }

    public function providerTypecastLoadOnly(): iterable
    {
        foreach (['integer', 'float', 'boolean', 'date', 'time', 'datetime', 'atk4_money'] as $type) {
            yield [[], ['type' => $type], null, '', false];
        }

        yield [[], ['type' => 'string'], null, '', false];
        yield [[], ['type' => 'text'], null, '', false];
        yield [[], ['type' => 'string'], null, ' ', false];
        yield [[], ['type' => 'string'], null, " \r\r\n ", false];
        yield [[], ['type' => 'string', 'nullable' => false], '', '', false];
        yield [[], ['type' => 'string', 'nullable' => false], '', ' ', false];
        yield [[], ['type' => 'string', 'nullable' => false], '', " \n ", false];
        yield [[], ['type' => 'text', 'required' => true], '', '', false];
        yield [[], ['type' => 'text'], "\n0", "\n0", false];
        yield [[], ['type' => 'text'], "\n0", "\r0", false];
        yield [[], ['type' => 'text'], "\n0", "\r\n0", false];
        yield [[], ['type' => 'text', 'nullable' => false], '', '', false];

        yield [[], ['type' => 'boolean'], false, '0', false];
        yield [[], ['type' => 'boolean'], true, '1', false];

        yield [[], ['type' => 'integer'], 0, '0.4', false];
        yield [[], ['type' => 'integer'], 1, '1.49', false];
        // yield [[], ['type' => 'integer'], 2, '1.5', false];
        yield [[], ['type' => 'integer'], -1, '-1.49', false];
        // yield [[], ['type' => 'integer'], -2, '-1.5', false];

        yield [[], ['type' => 'float'], 1.0, '1', false];
        yield [[], ['type' => 'float'], 0.0, '0', false];
        yield [[], ['type' => 'float'], 0.3, '.3', false];
        yield [[], ['type' => 'float'], -0.3, '-.3', false];
        yield [[], ['type' => 'float'], 0.3, '+00.3', false];
        yield [[], ['type' => 'float'], -0.3, '-00.300', false];

        yield [[], ['type' => 'atk4_money'], 2.0, '€2', false];
        yield [[], ['type' => 'atk4_money'], 2.0, '$2', false];
        yield [[], ['type' => 'atk4_money'], 2.0, '2€', false];
        yield [[], ['type' => 'atk4_money'], 2.0, '2$', false];
        yield [[], ['type' => 'atk4_money'], -1.3, '€-1.3', false];
        yield [[], ['type' => 'atk4_money'], -1.3, '-1.3$', false];
        yield [[], ['type' => 'atk4_money'], 0.3, '€.3', false];
        yield [[], ['type' => 'atk4_money'], 0.3, '.3$', false];
        yield [[], ['type' => 'atk4_money'], -0.3, '€-.3', false];
        yield [[], ['type' => 'atk4_money'], -0.3, '-.3$', false];
        // yield [[], ['type' => 'atk4_money'], 4.2, '4€2', false];
        // yield [[], ['type' => 'atk4_money'], -4.2, '-4$2', false];
    }
}
