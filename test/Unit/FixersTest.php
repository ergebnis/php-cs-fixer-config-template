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

use Ergebnis\PhpCsFixer;
use Ergebnis\PhpCsFixer\Config;
use PhpCsFixer\Fixer;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(Config\Fixers::class)]
final class FixersTest extends Framework\TestCase
{
    public function testEmptyReturnsFixers(): void
    {
        $fixers = Config\Fixers::empty();

        self::assertSame([], $fixers->toArray());
    }

    public function testFromFixersReturnsFixers(): void
    {
        $value = [
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
        ];

        $fixers = Config\Fixers::fromFixers(...$value);

        self::assertSame($value, $fixers->toArray());
    }

    public function testFromIterableRejectsInvalidValue(): void
    {
        $value = [
            self::createStub(Fixer\FixerInterface::class),
            new \stdClass(),
            self::createStub(Fixer\FixerInterface::class),
        ];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            \sprintf(
                'Expected iterable to contain only instances of %s, got %s instead.',
                Fixer\FixerInterface::class,
                \stdClass::class,
            ),
        );

        Config\Fixers::fromIterable($value);
    }

    public function testFromIterableReturnsFixersWhenValueIsArray(): void
    {
        $value = [
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
        ];

        $fixers = Config\Fixers::fromIterable($value);

        self::assertSame($value, $fixers->toArray());
    }

    public function testFromIterableReturnsFixersWhenValueIsTraversable(): void
    {
        $value = [
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
        ];

        $iterable = new \ArrayIterator($value);

        $fixers = Config\Fixers::fromIterable($iterable);

        self::assertSame($value, $fixers->toArray());
    }

    public function testMergeReturnsFixersMergedWithFixers(): void
    {
        $one = Config\Fixers::fromFixers(
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
        );

        $two = Config\Fixers::fromFixers(
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
        );

        $mutated = $one->merge($two);

        self::assertNotSame($one, $mutated);
        self::assertNotSame($two, $mutated);

        $expected = Config\Fixers::fromFixers(...\array_merge(
            $one->toArray(),
            $two->toArray(),
        ));

        self::assertEquals($expected, $mutated);
    }
}
