# php-cs-fixer-config-template

[![Integrate](https://github.com/ergebnis/php-cs-fixer-config-template/workflows/Integrate/badge.svg)](https://github.com/ergebnis/php-cs-fixer-config-template/actions)
[![Merge](https://github.com/ergebnis/php-cs-fixer-config-template/workflows/Merge/badge.svg)](https://github.com/ergebnis/php-cs-fixer-config-template/actions)
[![Release](https://github.com/ergebnis/php-cs-fixer-config-template/workflows/Release/badge.svg)](https://github.com/ergebnis/php-cs-fixer-config-template/actions)
[![Renew](https://github.com/ergebnis/php-cs-fixer-config-template/workflows/Renew/badge.svg)](https://github.com/ergebnis/php-cs-fixer-config-template/actions)

[![Code Coverage](https://codecov.io/gh/ergebnis/php-cs-fixer-config-template/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/php-cs-fixer-config-template)
[![Type Coverage](https://shepherd.dev/github/ergebnis/php-cs-fixer-config-template/coverage.svg)](https://shepherd.dev/github/ergebnis/php-cs-fixer-config-template)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/php-cs-fixer-config-template/v/stable)](https://packagist.org/packages/ergebnis/php-cs-fixer-config-template)
[![Total Downloads](https://poser.pugx.org/ergebnis/php-cs-fixer-config-template/downloads)](https://packagist.org/packages/ergebnis/php-cs-fixer-config-template)
[![Monthly Downloads](http://poser.pugx.org/ergebnis/php-cs-fixer-config-template/d/monthly)](https://packagist.org/packages/ergebnis/php-cs-fixer-config-template)

Provides a configuration factory and a rule set for [`friendsofphp/php-cs-fixer`](http://github.com/FriendsOfPHP/PHP-CS-Fixer).

## Installation

Run

```sh
composer require --dev ergebnis/php-cs-fixer-config-template
```

## Usage

### Configuration

Pick one of the rule sets:

- [`Ergebnis\PhpCsFixer\RuleSet\Custom`](src/RuleSet/Custom.php)

Create a configuration file `.php-cs-fixer.php` in the root of your project:

```php
<?php

use Ergebnis\PhpCsFixer\Config;

$config = Config\Factory::fromRuleSet(new Config\RuleSet\Custom());

$config->getFinder()->in(__DIR__);
$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php-cs-fixer.cache');

return $config;
```

### Git

All configuration examples use the caching feature, and if you want to use it as well, you should add the cache directory to `.gitignore`:

```diff
+ /.build/
 /vendor/
```

:bulb: Personally, I prefer to use a `.build` directory for storing build artifacts.

### Configuration with header

:bulb: Optionally specify a header:

```diff
 <?php

 use Ergebnis\PhpCsFixer\Config;

+$header = <<<EOF
+Copyright (c) 2022 Andreas Möller
+
+For the full copyright and license information, please view
+the LICENSE file that was distributed with this source code.
+
+@see https://github.com/ergebnis/php-cs-fixer-config-template
+EOF;

-$config = Config\Factory::fromRuleSet(new Config\RuleSet\Custom());
+$config = Config\Factory::fromRuleSet(new Config\RuleSet\Custom($header));

 $config->getFinder()->in(__DIR__);
 $config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php-cs-fixer.cache');

 return $config;
```

This will enable and configure the [`HeaderCommentFixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/v2.1.1/src/Fixer/Comment/HeaderCommentFixer.php), so that
file headers will be added to PHP files, for example:

```php
<?php

/**
 * Copyright (c) 2022 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/php-cs-fixer-config-template
 */
```

### Configuration with override rules

:bulb: Optionally override rules from a rule set by passing in an array of rules to be merged in:

```diff
 <?php

 use Ergebnis\PhpCsFixer\Config;

-$config = Config\Factory::fromRuleSet(new Config\RuleSet\Custom());
+$config = Config\Factory::fromRuleSet(new Config\RuleSet\Custom(), [
+    'mb_str_functions' => false,
+    'strict_comparison' => false,
+]);

 $config->getFinder()->in(__DIR__);
 $config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php-cs-fixer.cache');

 return $config;
```

### Makefile

If you like [`Makefile`](https://www.gnu.org/software/make/manual/make.html#Introduction)s, create a `Makefile` with a `coding-standards` target:

```diff
+.PHONY: coding-standards
+coding-standards: vendor
+	 mkdir -p .build/php-cs-fixer
+    vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff --verbose

 vendor: composer.json composer.lock
     composer validate
     composer install
```

Run

```sh
make coding-standards
```

to automatically fix coding standard violations.

### Composer script

If you like [`composer` scripts](https://getcomposer.org/doc/articles/scripts.md), add a `coding-standards` script to `composer.json`:

```diff
 {
   "name": "foo/bar",
   "require": {
     "php": "^7.3",
   },
   "require-dev": {
     "ergebnis/php-cs-fixer-config-template": "~1.0.0"
+  },
+  "scripts": {
+    "coding-standards": [
+      "mkdir -p .build/php-cs-fixer",
+      "php-cs-fixer fix --diff --verbose"
+    ]
   }
 }
```

Run

```sh
composer coding-standards
```

to automatically fix coding standard violations.

### GitHub Actions

If you like [GitHub Actions](https://github.com/features/actions), add a `coding-standards` job to your workflow:

```diff
 on:
   pull_request: null
   push:
     branches:
       - main

 name: "Integrate"

 jobs:
+  coding-standards:
+    name: "Coding Standards"
+
+    runs-on: ubuntu-latest
+
+    strategy:
+      matrix:
+        php-version:
+          - "8.0"
+
+    steps:
+      - name: "Checkout"
+        uses: "actions/checkout@v2"
+
+      - name: "Set up PHP"
+        uses: "shivammathur/setup-php@v2"
+        with:
+          coverage: "none"
+          php-version: "${{ matrix.php-version }}"
+
+      - name: "Cache dependencies installed with composer"
+        uses: "actions/cache@v2"
+        with:
+          path: "~/.composer/cache"
+          key: "php-${{ matrix.php-version }}-composer-${{ hashFiles('composer.lock') }}"
+          restore-keys: "php-${{ matrix.php-version }}-composer-"
+
+      - name: "Install locked dependencies with composer"
+        run: "composer install --ansi --no-interaction --no-progress --no-suggest"
+
+      - name: "Create cache directory for friendsofphp/php-cs-fixer"
+        run: mkdir -p .build/php-cs-fixer
+
+      - name: "Cache cache directory for friendsofphp/php-cs-fixer"
+        uses: "actions/cache@v2"
+        with:
+          path: "~/.build/php-cs-fixer"
+          key: "php-${{ matrix.php-version }}-php-cs-fixer-${{ github.ref_name }}"
+          restore-keys: |
+            php-${{ matrix.php-version }}-php-cs-fixer-main
+            php-${{ matrix.php-version }}-php-cs-fixer-
+
+      - name: "Run friendsofphp/php-cs-fixer"
+        run: "vendor/bin/php-cs-fixer fix --ansi --config=.php-cs-fixer.php --diff --dry-run --verbose"
```

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](https://github.com/ergebnis/.github/blob/main/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.

Please have a look at [`LICENSE.md`](LICENSE.md).

## Credits

This project is inspired by [`ergebnis/php-cs-fixer-config`](https://github.com/ergebnis/php-cs-fixer-config).

## Curious what I am up to?

Follow me on [Twitter](https://twitter.com/intent/follow?screen_name=localheinz)!
