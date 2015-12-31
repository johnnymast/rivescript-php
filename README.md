RiveScript-PHP
==============
[![Travis CI](https://img.shields.io/travis/vulcan-project/rivescript-php.svg?style=flat-square)](https://travis-ci.org/vulcan-project/rivescript-php)
[![Source](http://img.shields.io/badge/source-vulcan--project/rivescript--php-blue.svg?style=flat-square)](https://github.com/vulcan-project/rivescript-php)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

**Note:** This is still in very early development. Don't have much to show right now.

This is a RiveScript interpreter library for PHP. RiveScript is a scripting language for chatterbots, making it easy to write trigger/response pairs for building up a bot's intelligence.

The package follows the FIG standards PSR-1, PSR-2, and PSR-4 to ensure a high level of interoperability between shared PHP code.

## Quick Installation
Simply install the package through Composer.

```
composer require vulcan/rivescript
```

## Integration
The Vulcan RiveScript package is framework agnostic. As such, the package can be used as is with native PHP, or with your favorite framework.

### Laravel 5
The Vulcan RiveScript package comes with optional support for Laravel 5 by means of a Service Provider and Facade for easy integration.

After installing, open your `app` Laravel config file and add the following:

#### Providers
```php
Vulcan\Rivescript\Laravel\RivescriptServiceProvider::class
```

##### Aliases
```php
'RiveScript' => Vulcan\Rivescript\Laravel\Facades\Rivescript::class
```
