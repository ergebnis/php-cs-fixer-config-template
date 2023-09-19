<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/php-cs-fixer-config-template
 */

namespace Ergebnis\PhpCsFixer\Config;

final class RuleSet
{
    private function __construct(
        private readonly Fixers $customFixers,
        private readonly Name $name,
        private readonly PhpVersion $phpVersion,
        private readonly Rules $rules,
    ) {
    }

    public static function create(
        Fixers $customFixers,
        Name $name,
        PhpVersion $phpVersion,
        Rules $rules,
    ): self {
        return new self(
            $customFixers,
            $name,
            $phpVersion,
            $rules,
        );
    }

    /**
     * Returns custom fixers required by this rule set.
     */
    public function customFixers(): Fixers
    {
        return $this->customFixers;
    }

    /**
     * Returns the name of the rule set.
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * Returns the minimum required PHP version.
     */
    public function phpVersion(): PhpVersion
    {
        return $this->phpVersion;
    }

    /**
     * Returns rules along with their configuration.
     */
    public function rules(): Rules
    {
        return $this->rules;
    }

    /**
     * Returns a new rule set with custom fixers.
     */
    public function withCustomFixers(Fixers $customFixers): self
    {
        return new self(
            $this->customFixers->merge($customFixers),
            $this->name,
            $this->phpVersion,
            $this->rules,
        );
    }

    /**
     * Returns a new rule set with merged rules.
     */
    public function withRules(Rules $rules): self
    {
        return new self(
            $this->customFixers,
            $this->name,
            $this->phpVersion,
            $this->rules->merge($rules),
        );
    }

    /**
     * Returns a new rule set with rules where the header_comment fixer is enabled to add a header.
     *
     * @see https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/v3.27.0/doc/rules/comment/header_comment.rst
     */
    public function withHeader(string $header): self
    {
        return new self(
            $this->customFixers,
            $this->name,
            $this->phpVersion,
            $this->rules->merge(Rules::fromArray([
                'header_comment' => [
                    'comment_type' => 'PHPDoc',
                    'header' => \trim($header),
                    'location' => 'after_declare_strict',
                    'separate' => 'both',
                ],
            ])),
        );
    }
}
