<?php

declare(strict_types=1);

/**
 * Copyright (c) 2019-2020 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/php-cs-fixer-config-template
 */

namespace Ergebnis\PhpCsFixer\Config;

interface RuleSet
{
    /**
     * Returns the name of the rule set.
     */
    public function name(): string;

    /**
     * Returns an array of rules along with their configuration.
     *
     * @return array<string, array|bool>
     */
    public function rules(): array;

    /**
     * Returns the minimum required PHP version (PHP_VERSION_ID).
     *
     * @see http://php.net/manual/en/reserved.constants.php
     */
    public function targetPhpVersion(): int;
}
