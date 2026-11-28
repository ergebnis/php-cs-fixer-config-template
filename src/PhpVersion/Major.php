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

namespace Ergebnis\PhpCsFixer\Config\PhpVersion;

final class Major
{
    private function __construct(private readonly int $value)
    {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function fromInt(int $value): self
    {
        if (0 > $value) {
            throw new \InvalidArgumentException(\sprintf(
                'Value needs to greater than or equal to 0, but %d is not.',
                $value,
            ));
        }

        return new self($value);
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
