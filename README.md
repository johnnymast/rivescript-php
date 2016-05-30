RiveScript-PHP
==============
[![Travis CI](https://img.shields.io/travis/vulcan-project/rivescript-php.svg?style=flat-square)](https://travis-ci.org/vulcan-project/rivescript-php)
[![Source](http://img.shields.io/badge/source-vulcan--project/rivescript--php-blue.svg?style=flat-square)](https://github.com/vulcan-project/rivescript-php)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

**Note:** This is still in very early development. Don't have much to show right now.

This is a RiveScript interpreter library for PHP. See the below [Working Draft](#working-draft) section for more information.

The package follows the FIG standards PSR-1, PSR-2, and PSR-4 to ensure a high level of interoperability between shared PHP code.

## Quick Installation
Simply install the package through Composer.

```
composer require vulcan/rivescript
```

## Integration
The RiveScript PHP interpreter is framework agnostic. As such, the interpreter can be used as is with native PHP, or with your favorite framework.

---

# Working Draft
RiveScript is an interpreted scripting language for giving responses to chatterbots and other intelligent chatting entities in a simple trigger/reply format. The scripting language is intended to be simplistic and easy to learn and manage.

## Vocabulary
- **RiveScript**
  RiveScript is the name of the scripting language that this document explains.
- **Interpreter**
  The RiveScript interpreter is a program or library in another programming language that loads and parses a RiveScript document.
- **RiveScript Document**
  A RiveScript Document is a text file containing RiveScript code.
- **Bot**
  A Bot (short for "robot") is the artificial entity that is represented by an instance of a RiveScript interpreter object. That is, when you create a new interpreter object and load a set of RiveScript Documents, that becomes the "brain" of the bot.
- **Bot Variable**
  A variable that describes the bot, such as name, age, or any other detail you want to define for the bot.
- **Client Variable**
  A variable that the bot keeps about a specific client, or user of the bot. Usually as the client tells the bot information about itself, the bot could save this information into Client Variables and recite it later.

## Format
A RiveScript document should be parsed line by line, and preferably arranged in the interpreter's memory in an efficient way.

The first character on each line should be the **command**, and the rest of the line is the command's **arguments**. The **command** should be a single character that is not a number or letter.

In its most simplest form, a valid RiveScript trigger/response pair looks like this:

```
+ hello bot
- Hello, human.
```

## Whitespace
A RiveScript Interpreter should ignore leading and trailing whitespace characters on any line. It should also ignore whitespace characters surrounding individual arguments of a RiveScript command, where applicable. That is to say, the following two lines should be interpreted as being exactly the same:

```
! global debug = 1
!    global    debug=    1
```
