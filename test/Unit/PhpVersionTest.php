<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/php-cs-fixer-config-template
 */

namespace Ergebnis\PhpCsFixer\Config\Test\Unit;

use Ergebnis\DataProvider;
use Ergebnis\PhpCsFixer;
use Ergebnis\PhpCsFixer\Config;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Config\PhpVersion::class)]
#[Framework\Attributes\UsesClass(Config\PhpVersion\Major::class)]
#[Framework\Attributes\UsesClass(Config\PhpVersion\Minor::class)]
#[Framework\Attributes\UsesClass(Config\PhpVersion\Patch::class)]
final class PhpVersionTest extends Framework\TestCase
{
    use Config\Test\Util\Helper;

    public function testCreateReturnsPhpVersion(): void
    {
        $faker = self::faker();

        $major = Config\PhpVersion\Major::fromInt($faker->numberBetween(0));
        $minor = Config\PhpVersion\Minor::fromInt($faker->numberBetween(0, 99));
        $patch = Config\PhpVersion\Patch::fromInt($faker->numberBetween(0, 99));

        $phpVersion = Config\PhpVersion::create(
            $major,
            $minor,
            $patch,
        );

        self::assertSame($major, $phpVersion->major());
        self::assertSame($minor, $phpVersion->minor());
        self::assertSame($patch, $phpVersion->patch());

        $expected = \sprintf(
            '%d.%d.%d',
            $major->toInt(),
            $minor->toInt(),
            $patch->toInt(),
        );

        self::assertSame($expected, $phpVersion->toString());
    }

    #[Framework\Attributes\DataProviderExternal(DataProvider\IntProvider::class, 'lessThanZero')]
    public function testFromIntRejectsInvalidValue(int $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Value needs to be greater than or equal to 0, but %d is not.',
            $value,
        ));

        Config\PhpVersion::fromInt($value);
    }

    #[Framework\Attributes\DataProviderExternal(DataProvider\IntProvider::class, 'zero')]
    #[Framework\Attributes\DataProviderExternal(DataProvider\IntProvider::class, 'greaterThanZero')]
    public function testFromIntReturnsPhpVersion(int $value): void
    {
        $phpVersion = Config\PhpVersion::fromInt($value);

        self::assertSame($value, $phpVersion->toInt());
    }

    public function testCurrentReturnsPhpVersion(): void
    {
        $phpVersion = Config\PhpVersion::current();

        self::assertSame(\PHP_VERSION_ID, $phpVersion->toInt());
    }

    public function testIsLessThanReturnsFalseWhenValueIsGreater(): void
    {
        $one = Config\PhpVersion::fromInt(\PHP_VERSION_ID + 1);
        $two = Config\PhpVersion::fromInt(\PHP_VERSION_ID);

        self::assertFalse($one->isLessThan($two));
    }

    public function testIsLessThanReturnsFalseWhenValueIsSame(): void
    {
        $value = \PHP_VERSION_ID;

        $one = Config\PhpVersion::fromInt($value);
        $two = Config\PhpVersion::fromInt($value);

        self::assertFalse($one->isLessThan($two));
    }

    public function testIsLessThanReturnsTrueWhenValueIsLess(): void
    {
        $one = Config\PhpVersion::fromInt(\PHP_VERSION_ID);
        $two = Config\PhpVersion::fromInt(\PHP_VERSION_ID + 1);

        self::assertTrue($one->isLessThan($two));
    }
}
