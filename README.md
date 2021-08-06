# RiveScript-PHP
[![Source](http://img.shields.io/badge/source-axiom--labs/rivescript--php-blue.svg?style=flat-square)](https://github.com/axiom-labs/rivescript-php)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)
[![Build & Unit Test](https://github.com/johnnymast/rivescript-php/actions/workflows/phpunit.yml/badge.svg)](https://github.com/johnnymast/rivescript-php/actions/workflows/phpunit.yml)
[![Phpcs](https://github.com/johnnymast/rivescript-php/actions/workflows/Phpcs.yaml/badge.svg)](https://github.com/johnnymast/rivescript-php/actions/workflows/Phpcs.yaml)

This is a RiveScript interpreter library for PHP. RiveScript is a simple scripting language for chatbots with a friendly, easy to learn syntax.

The package follows the FIG standards PSR-1, PSR-2, and PSR-4 to ensure a high level of interoperability between shared PHP code.

## Quick Installation
Simply install the package through Composer.

```
composer require axiom/rivescript
```

## Integration
The RiveScript PHP interpreter is framework agnostic. As such, the interpreter can be used as is with native PHP, or with your favorite framework.

<i>example.rive</a>
```rivescript

+ hello bot
- Hello Human

```

```php

require 'vendor/autoload.php';
use \Axiom\Rivescript\Rivescript;

$message = 'hello bot';
$rivescript = new Rivescript();
$rivescript->load('example.rive');

echo $rivescript->reply($message);

```

<i>Output</i>
```bash
Hello Human
```
---

# Working Draft

The RiveScript Working Draft (WD) is a document that defines the standards for how RiveScript should work, from an implementation-agnostic point of view. The Working Draft should be followed when contributing to the RiveScript-PHP interpreter. If any of the current implementations don't do what the Working Draft says they should, this is considered to be a bug and you can file a bug report or send a pull request.

You may find the latest version on the RiveScript website at http://www.rivescript.com/wd/RiveScript.