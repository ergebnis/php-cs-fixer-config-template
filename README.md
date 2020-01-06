# php-cs-fixer-config-template

[![Continuous Deployment](https://github.com/ergebnis/php-cs-fixer-config-template/workflows/Continuous%20Deployment/badge.svg)](https://github.com/ergebnis/php-cs-fixer-config-template/actions)
[![Continuous Integration](https://github.com/ergebnis/php-cs-fixer-config-template/workflows/Continuous%20Integration/badge.svg)](https://github.com/ergebnis/php-cs-fixer-config-template/actions)
[![Code Coverage](https://codecov.io/gh/ergebnis/php-cs-fixer-config-template/branch/master/graph/badge.svg)](https://codecov.io/gh/ergebnis/php-cs-fixer-config-template)
[![Type Coverage](https://shepherd.dev/github/ergebnis/php-cs-fixer-config-template/coverage.svg)](https://shepherd.dev/github/ergebnis/php-cs-fixer-config-template)
[![Latest Stable Version](https://poser.pugx.org/ergebnis/php-cs-fixer-config-template/v/stable)](https://packagist.org/packages/ergebnis/php-cs-fixer-config-template)
[![Total Downloads](https://poser.pugx.org/ergebnis/php-cs-fixer-config-template/downloads)](https://packagist.org/packages/ergebnis/php-cs-fixer-config-template)

Provides a configuration factory and multiple rule sets for [`friendsofphp/php-cs-fixer`](http://github.com/FriendsOfPHP/PHP-CS-Fixer).

## Motivation

### Shareability

The primary motivation for this package is the necessity to easily share the configuration for `friendsofphp/php-cs-fixer` across multiple packages within one organization.

### Configuration out of the box

Out of the box, the process for configuring [`friendsofphp/php-cs-fixer`](http://github.com/FriendsOfPHP/PHP-CS-Fixer) currently works as follows:

- create and configure an instance of [`PhpCsFixer\Finder`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/v2.16.0/src/Finder.php)
- create and configure an instance of [`PhpCsFixer\Config`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/v2.16.0/src/Config.php)

For a typical project, the corresponding configuration file `.php_cs` could look like this:

```php
<?php

/**
 * Copyright (c) 2019 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/php-cs-fixer-config-template
 */

declare(strict_types=1);

$header = <<<'EOF'
Copyright (c) 2019 Andreas Möller

For the full copyright and license information, please view
the LICENSE file that was distributed with this source code.

@see https://github.com/ergebnis/php-cs-fixer-config-template
EOF;

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(false)
    ->in(__DIR__)
    ->exclude([
        '.build',
        '.dependabot',
        '.github',
    ])
    ->name('.php_cs');

return PhpCsFixer\Config::create()
    ->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php_cs.cache')
    ->setFinder($finder)
    ->setRules([
        '@PSR2' => true,
        // ...
        'header_comment' => [
            'comment_type' => 'PHPDoc',
            'header' => \trim($header),
            'location' => 'after_declare_strict',
            'separate' => 'both',
        ],
        // ...
    ]);
```

### Sharing plain PHP files

If we created a package and shared plain PHP files returning the rules, the configuration could look like this:

```diff
 @see https://github.com/ergebnis/php-cs-fixer-config-template
 EOF;

+$rules = require __DIR__ . 'vendor/ergebnis/php-cs-fixer-config/rules.php';
+
 $finder = PhpCsFixer\Finder::create()
     ->ignoreDotFiles(false)
     ->in(__DIR__)
@@ -24,14 +26,11 @@ $finder = PhpCsFixer\Finder::create()
 return PhpCsFixer\Config::create()
     ->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php_cs.cache')
     ->setFinder($finder)
-    ->setRules([
-        '@PSR2' => true,
-        // ...
+    ->setRules(\array_merge($rules, [
         'header_comment' => [
             'comment_type' => 'PHPDoc',
             'header' => \trim($header),
             'location' => 'after_declare_strict',
             'separate' => 'both',
         ],
-        // ...
-    ]);
+    ]));
```

However, dealing with paths here is not so much fun. If we could use a class instead, we would only need to know the class name, not the path to it, and that would be a lot better.

### Sharing configuration classes

Nonetheless, if we decided to create an implementation of [`PhpCsFixer\ConfigInterface`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/v2.16.0/src/ConfigInterface.php), we would soon realize that this interface has a lot of methods that we would prefer not to implement.

### Fixer behaviour might depend on PHP version

Now, there's another issue: there are fixers which behave differently depending on the PHP version they are run on, and we want to make sure that we run those fixers only when we want to.

### Sharing rule sets and a factory

That is, what we eventually want is a rule set that provides

- a name for the configuration (which will be displayed when we run [`php-cs-fixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/v2.16.0/php-cs-fixer))
- the rules we want to use
- a target PHP version

as well as a factory which allows us to

- create an implementation of [`PhpCsFixer\ConfigInterface`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/v2.16.0/src/ConfigInterface.php) from the rule set
- prevents us from creating and using a configuration based on a rule set which targets a different PHP version

The rule set needs to implement [`Ergebnis\PhpCsFixer\Config\RuleSet`](src/RuleSet.php) - and this can be done by extending from [`Ergebnis\PhpCsFixer\Config\RuleSet\AbstractRuleSet`](src/RuleSet/AbstractRuleSet.php), which additionally allows specifying an optional header comment via constructor injection (the header comment will likely be different from one project to another).

The factory is provided by [`Ergebnis\PhpCsFixer\Config\Factory`](src/Factory.php), which accepts

- a rule set
- an optional array of rules that should override the rules otherwise provided by the rule set
- and which throws an exception when the current PHP version is smaller than the targeted PHP version.

When using the factory and the custom rule set, the configuration file could look like this:

```diff
  * @see https://github.com/ergebnis/php-cs-fixer-config-template
  */

+use Ergebnis\PhpCsFixer\Config;
+
 $header = <<<'EOF'
 Copyright (c) 2019 Andreas Möller

@@ -20,9 +22,9 @@ the LICENSE file that was distributed with this source code.
 @see https://github.com/ergebnis/php-cs-fixer-config-template
 EOF;

-$rules = require __DIR__ . 'vendor/ergebnis/php-cs-fixer-config/rules.php';
+$config = Config\Factory::fromRuleSet(new Config\RuleSet\Custom($header));

-$finder = PhpCsFixer\Finder::create()
+$config->getFinder()
     ->ignoreDotFiles(false)
     ->in(__DIR__)
     ->exclude([
@@ -32,14 +34,6 @@ $finder = PhpCsFixer\Finder::create()
     ])
     ->name('.php_cs');

-return PhpCsFixer\Config::create()
-    ->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php_cs.cache')
-    ->setFinder($finder)
-    ->setRules(\array_merge($rules, [
-        'header_comment' => [
-            'comment_type' => 'PHPDoc',
-            'header' => \trim($header),
-            'location' => 'after_declare_strict',
-            'separate' => 'both',
-        ],
-    ]));
+$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php_cs.cache');
+
+return $config;
```

### Discovery of newly added fixers

But that's not all - using the [`Ergebnis\PhpCsFixer\Config\Test\Unit\RuleSet\AbstractRuleSetTestCase`](test/Unit/RuleSet/AbstractRuleSetTestCase.php), we can also ensure that a configuration:

- is the same as expected (a bit boring, compare [`Ergebnis\PhpCsFixer\Config\RuleSet\Custom`](src/RuleSet/Custom.php) with [`Ergebnis\PhpCsFixer\Config\Test\Unit\RuleSet\Custom`](test/Unit/RuleSet/CustomTest.php)
- explicitly enables or disables all (!) available fixers

The latter is rather interesting - when using  [Dependabot](https://dependabot.com), new releases of  `friendsofphp/php-cs-fixer` that add new fixers will result in pull requests with failing builds. Otherwise, we might not be aware of these new fixers.

:tipping_hand_man: Personally, I believe this is a lot better than sharing PHP files returning arrays of rules.

## Installation

Run

```sh
$ composer require --dev ergebnis/php-cs-fixer-config-template
```

## Usage

### Configuration

Pick one of the rule sets:

* [`Ergebnis\PhpCsFixer\RuleSet\Custom`](src/RuleSet/Custom.php)

Create a configuration file `.php_cs` in the root of your project:

```php
<?php

use Ergebnis\PhpCsFixer\Config;

$config = Config\Factory::fromRuleSet(new Config\RuleSet\Custom());

$config->getFinder()->in(__DIR__);
$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/php_cs.cache');

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
+Copyright (c) 2019 Andreas Möller
+
+For the full copyright and license information, please view
+the LICENSE file that was distributed with this source code.
+
+@see https://github.com/ergebnis/php-cs-fixer-config-template
+EOF;

-$config = Config\Factory::fromRuleSet(new Config\RuleSet\Custom());
+$config = Config\Factory::fromRuleSet(new Config\RuleSet\Custom($header));

 $config->getFinder()->in(__DIR__);
 $config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/php_cs.cache');

 return $config;
```

This will enable and configure the [`HeaderCommentFixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/v2.1.1/src/Fixer/Comment/HeaderCommentFixer.php), so that
file headers will be added to PHP files, for example:

```php
<?php

/**
 * Copyright (c) 2019 Andreas Möller
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
 $config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/php_cs.cache');

 return $config;
```

### Makefile

If you like [`Makefile`](https://www.gnu.org/software/make/manual/make.html#Introduction)s, create a `Makefile` with a `coding-standards` target:

```diff
+.PHONY: coding-standards
+coding-standards: vendor
+	 mkdir -p .build/php-cs-fixer
+    vendor/bin/php-cs-fixer fix --config=.php_cs --diff --verbose

 vendor: composer.json composer.lock
     composer validate
     composer install
```

Run

```
$ make coding-standards
```

to automatically fix coding standard violations.

### Composer script

If you like [`composer` scripts](https://getcomposer.org/doc/articles/scripts.md), add a `coding-standards` script to `composer.json`:

```diff
 {
   "name": "foo/bar",
   "require": {
     "php": "^7.2",
   },
   "require-dev": {
     "ergebnis/php-cs-fixer-config": "~1.0.0"
+  },
+  "scripts": {
+    "coding-standards": [
+      "mkdir -p .build/php-cs-fixer",
+      "php-cs-fixer fix --diff --diff-format=udiff --verbose"
+    ]
   }
 }
```

Run

```
$ composer coding-standards
```

to automatically fix coding standard violations.

### GitHub Actions

If you like [GitHub Actions](https://github.com/features/actions), add a `coding-standards` job to your workflow:

```diff
 on:
   pull_request:
   push:
     branches:
       - master
     tags:
       - "**"

 name: "Continuous Integration"

 jobs:
+  coding-standards:
+    name: "Coding Standards"
+
+    runs-on: ubuntu-latest
+
+    steps:
+      - name: "Checkout"
+        uses: actions/checkout@v1.1.0
+
+      - name: "Disable Xdebug"
+        run: php7.2 --ini | grep xdebug | sed 's/,$//' | xargs sudo rm
+
+      - name: "Cache dependencies installed with composer"
+        uses: actions/cache@v1.0.2
+        with:
+          path: ~/.composer/cache
+          key: php7.2-composer-locked-${{ hashFiles('**/composer.lock') }}
+          restore-keys: |
+            php7.2-composer-locked-
+
+      - name: "Install locked dependencies with composer"
+        run: php7.2 $(which composer) install --no-interaction --no-progress --no-suggest
+
+      - name: "Create cache directory for friendsofphp/php-cs-fixer"
+        run: mkdir -p .build/php-cs-fixer
+
+      - name: "Cache cache directory for friendsofphp/php-cs-fixer"
+        uses: actions/cache@v1.0.2
+        with:
+          path: ~/.build/php-cs-fixer
+          key: php7.2-php-cs-fixer-${{ hashFiles('**/composer.lock') }}
+          restore-keys: |
+            php7.2-php-cs-fixer-
+
+      - name: "Run friendsofphp/php-cs-fixer"
+        run: php7.2 vendor/bin/php-cs-fixer fix --config=.php_cs --diff --diff-format=udiff --dry-run --verbose
```

### Travis

If you like [Travis CI](https://travis-ci.com), add a `coding-standards` stage to your jobs:

```diff
 language: php

 cache:
   directories:
     - $HOME/.composer/cache
+    - .build/php-cs-fixer

 jobs:
   include:
+    - stage: "Coding Standards"
+
+      php: 7.2
+
+      install:
+        - composer install --no-interaction --no-progress --no-suggest
+
+      before_script:
+        - mkdir -p .build/php-cs-fixer
+
+      script:
+        - vendor/bin/php-cs-fixer fix --config=.php_cs --diff --dry-run --verbose
```

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](.github/CONTRIBUTING.md).

## Code of Conduct

Please have a look at [`CODE_OF_CONDUCT.md`](.github/CODE_OF_CONDUCT.md).

## License

This package is licensed using the MIT License.
