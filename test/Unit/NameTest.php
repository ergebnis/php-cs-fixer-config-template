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
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(PhpCsFixer\Config\Name::class)]
#[Framework\Attributes\UsesClass(PhpCsFixer\Config\PhpVersion::class)]
#[Framework\Attributes\UsesClass(PhpCsFixer\Config\PhpVersion\Major::class)]
#[Framework\Attributes\UsesClass(PhpCsFixer\Config\PhpVersion\Minor::class)]
#[Framework\Attributes\UsesClass(PhpCsFixer\Config\PhpVersion\Patch::class)]
final class NameTest extends Framework\TestCase
{
    use PhpCsFixer\Config\Test\Util\Helper;

    #[Framework\Attributes\DataProviderExternal(DataProvider\StringProvider::class, 'blank')]
    #[Framework\Attributes\DataProviderExternal(DataProvider\StringProvider::class, 'empty')]
    public function testFromStringRejectsBlankOrEmptyString(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value can not be blank or empty.');

        PhpCsFixer\Config\Name::fromString($value);
    }

    public function testFromStringReturnsName(): void
    {
        $value = self::faker()->word();

        $name = PhpCsFixer\Config\Name::fromString($value);

        self::assertSame($value, $name->toString());
    }
}
