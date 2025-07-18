<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2025 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/php-cs-fixer-config-template
 */

namespace Ergebnis\PhpCsFixer\Config\Test\Unit\PhpVersion;

use Ergebnis\DataProvider;
use Ergebnis\PhpCsFixer\Config\PhpVersion;
use Ergebnis\PhpCsFixer\Config\Test;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(PhpVersion\Minor::class)]
final class MinorTest extends Framework\TestCase
{
    use Test\Util\Helper;

    #[Framework\Attributes\DataProviderExternal(DataProvider\IntProvider::class, 'lessThanZero')]
    public function testFromIntRejectsValueLessThanZero(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to be a greater than or equal to 0, but %d is not.',
            $value,
        ));

        PhpVersion\Minor::fromInt($value);
    }

    #[Framework\Attributes\DataProvider('provideValueGreaterThanNinetyNine')]
    public function testFromIntRejectsValueGreaterThanNinetyNine(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to be a less than or equal to 99, but %d is not.',
            $value,
        ));

        PhpVersion\Minor::fromInt($value);
    }

    /**
     * @return \Generator<string, array{0: int}>
     */
    public static function provideValueGreaterThanNinetyNine(): \Generator
    {
        $values = [
            'int-greater-than-one-hundred' => self::faker()->numberBetween(101, 999),
            'int-one-hundred' => 100,
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    #[Framework\Attributes\DataProvider('provideValidValue')]
    public function testFromIntReturnsMinor(int $value): void
    {
        $minor = PhpVersion\Minor::fromInt($value);

        self::assertSame($value, $minor->toInt());
    }

    /**
     * @return \Generator<string, array{0: int}>
     */
    public static function provideValidValue(): \Generator
    {
        $values = [
            'int-greater-than-one-less-than-ninetynine' => self::faker()->numberBetween(1, 99),
            'int-ninetynine' => 99,
            'int-one' => 1,
            'int-zero' => 0,
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }
}
