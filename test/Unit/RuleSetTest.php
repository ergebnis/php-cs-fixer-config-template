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

namespace Ergebnis\PhpCsFixer\Config\Test\Unit;

use Ergebnis\PhpCsFixer\Config\Fixers;
use Ergebnis\PhpCsFixer\Config\Name;
use Ergebnis\PhpCsFixer\Config\PhpVersion;
use Ergebnis\PhpCsFixer\Config\Rules;
use Ergebnis\PhpCsFixer\Config\RuleSet;
use Ergebnis\PhpCsFixer\Config\Test;
use PhpCsFixer\Fixer;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(RuleSet::class)]
#[Framework\Attributes\UsesClass(Name::class)]
#[Framework\Attributes\UsesClass(Fixers::class)]
#[Framework\Attributes\UsesClass(PhpVersion::class)]
#[Framework\Attributes\UsesClass(PhpVersion\Major::class)]
#[Framework\Attributes\UsesClass(PhpVersion\Minor::class)]
#[Framework\Attributes\UsesClass(PhpVersion\Patch::class)]
#[Framework\Attributes\UsesClass(Rules::class)]
final class RuleSetTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testCreateReturnsRuleSet(): void
    {
        $faker = self::faker();

        $customFixers = Fixers::fromFixers(
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
        );
        $name = Name::fromString($faker->word());
        $phpVersion = PhpVersion::create(
            PhpVersion\Major::fromInt($faker->numberBetween(0)),
            PhpVersion\Minor::fromInt($faker->numberBetween(0, 99)),
            PhpVersion\Patch::fromInt($faker->numberBetween(0, 99)),
        );
        $rules = Rules::fromArray([
            'header_comment' => false,
        ]);

        $ruleSet = RuleSet::create(
            $customFixers,
            $name,
            $phpVersion,
            $rules,
        );

        self::assertSame($customFixers, $ruleSet->customFixers());
        self::assertSame($name, $ruleSet->name());
        self::assertSame($phpVersion, $ruleSet->phpVersion());
        self::assertSame($rules, $ruleSet->rules());
    }

    public function testWithCustomFixersReturnsRuleSetWithMergedCustomFixers(): void
    {
        $faker = self::faker();

        $customFixers = Fixers::fromFixers(
            self::createStub(Fixer\FixerInterface::class),
            self::createStub(Fixer\FixerInterface::class),
        );

        $ruleSet = RuleSet::create(
            Fixers::fromFixers(
                self::createStub(Fixer\FixerInterface::class),
                self::createStub(Fixer\FixerInterface::class),
                self::createStub(Fixer\FixerInterface::class),
            ),
            Name::fromString($faker->word()),
            PhpVersion::create(
                PhpVersion\Major::fromInt($faker->numberBetween(0)),
                PhpVersion\Minor::fromInt($faker->numberBetween(0, 99)),
                PhpVersion\Patch::fromInt($faker->numberBetween(0, 99)),
            ),
            Rules::fromArray([
                'foo' => false,
                'quz' => true,
            ]),
        );

        $mutated = $ruleSet->withCustomFixers($customFixers);

        self::assertNotSame($ruleSet, $mutated);

        $expected = $ruleSet->customFixers()->merge($customFixers);

        self::assertEquals($expected, $mutated->customFixers());
        self::assertEquals($ruleSet->name(), $mutated->name());
        self::assertEquals($ruleSet->phpVersion(), $mutated->phpVersion());
        self::assertEquals($ruleSet->rules(), $mutated->rules());
    }

    public function testWithRulesReturnsRuleSetWithMergedRules(): void
    {
        $faker = self::faker();

        $rules = Rules::fromArray([
            'bar' => false,
            'foo' => true,
        ]);

        $ruleSet = RuleSet::create(
            Fixers::fromFixers(
                self::createStub(Fixer\FixerInterface::class),
                self::createStub(Fixer\FixerInterface::class),
                self::createStub(Fixer\FixerInterface::class),
            ),
            Name::fromString($faker->word()),
            PhpVersion::create(
                PhpVersion\Major::fromInt($faker->numberBetween(0)),
                PhpVersion\Minor::fromInt($faker->numberBetween(0, 99)),
                PhpVersion\Patch::fromInt($faker->numberBetween(0, 99)),
            ),
            Rules::fromArray([
                'foo' => false,
                'quz' => true,
            ]),
        );

        $mutated = $ruleSet->withRules($rules);

        self::assertNotSame($ruleSet, $mutated);

        self::assertEquals($ruleSet->customFixers(), $mutated->customFixers());
        self::assertEquals($ruleSet->name(), $mutated->name());
        self::assertEquals($ruleSet->phpVersion(), $mutated->phpVersion());

        $expected = $ruleSet->rules()->merge($rules);

        self::assertEquals($expected, $mutated->rules());
    }

    #[Framework\Attributes\DataProvider('provideValidHeader')]
    public function testWithHeaderReturnsRuleSetWithEnabledHeaderCommentFixer(string $header): void
    {
        $faker = self::faker();

        $ruleSet = RuleSet::create(
            Fixers::fromFixers(
                self::createStub(Fixer\FixerInterface::class),
                self::createStub(Fixer\FixerInterface::class),
                self::createStub(Fixer\FixerInterface::class),
            ),
            Name::fromString($faker->word()),
            PhpVersion::create(
                PhpVersion\Major::fromInt($faker->numberBetween(0)),
                PhpVersion\Minor::fromInt($faker->numberBetween(0, 99)),
                PhpVersion\Patch::fromInt($faker->numberBetween(0, 99)),
            ),
            Rules::fromArray([
                'foo' => false,
                'header_comment' => false,
                'quz' => true,
            ]),
        );

        $mutated = $ruleSet->withHeader($header);

        self::assertNotSame($ruleSet, $mutated);

        self::assertEquals($ruleSet->customFixers(), $mutated->customFixers());
        self::assertEquals($ruleSet->name(), $mutated->name());
        self::assertEquals($ruleSet->phpVersion(), $mutated->phpVersion());

        $expected = $ruleSet->rules()->merge(Rules::fromArray([
            'header_comment' => [
                'comment_type' => 'PHPDoc',
                'header' => \trim($header),
                'location' => 'after_declare_strict',
                'separate' => 'both',
            ],
        ]));

        self::assertEquals($expected, $mutated->rules());
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideValidHeader(): \Generator
    {
        $values = [
            'string-empty' => '',
            'string-not-empty' => 'foo',
            'string-with-line-feed-only' => "\n",
            'string-with-spaces-only' => ' ',
            'string-with-tab-only' => "\t",
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }
}
