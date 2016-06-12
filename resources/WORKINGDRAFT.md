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

## Commands

### ! Definition
The ! command is for defining variables within RiveScript. It's used to define information about the bot, define global arrays that can be used in multiple triggers, or override interpreter globals such as debug mode.

The format of the `!` command is as follows:

```
! type name = value
```

Where type is one of `version`, `global`, `var`, `array`, `sub`, or `person`. The name is the name of the variable being defined, and value is the value of said variable.

Whitespace surrounding the `=` sign should be stripped out.

Setting a value to `<undef>` will undefine the variable (deleting it or uninitializing it, depending on the implementation).

The variable types supported are detailed as follows:

#### version
It's highly recommended practice that new RiveScript documents explicitly define the version of RiveScript that they are following. RiveScript 2.00 has some compatibility issues with the old 1.x line (see "REVERSE COMPATIBILITY"). Newer RiveScript versions should encourage that RiveScript documents define their own version numbers.

```
! version = 2.00
```

#### global
This should override a global variable at the interpreter level. The obvious variable name might be "debug" (to enable/disable debugging within the RiveScript interpreter).

The interpreter should take extra care not to allow reserved globals to be overridden by this command in ways that might break the interpreter.

Examples:

```
! global debug = 1
```

#### var
This should define a "bot variable" for the bot. This should only be used in an initialization sense; that is, as the interpreter loads the document, it should define the bot variable as it reads in this line. If you'd want to redefine or alter the value of a bot variable, you should do so using a tag inside of a RiveScript document (see "TAGS").

Examples:

```
! var name      = RiveScript Bot
! var age       = 0
! var gender    = androgynous
! var location  = Cyberspace
! var generator = RiveScript
```

#### array
This will create an array of strings, which can then be used later in triggers (see "+ TRIGGER"). If the array contains single words, separating the words with a space character is fine. If the array contains items with multiple words in them, separate the entries with a pipe symbol ("`|`").

Examples:

```
! array colors = red green blue cyan magenta yellow black white orange brown
! array be     = is are was were
! array whatis = what is|what are|what was|what were
```

Arrays have special treatment when spanned over multiple lines. Each extension of the array data is treated individually. For example, to break an array of many single-words into multiple lines of RiveScript code:

```
! array colors = red green blue cyan
^ magenta yellow black white
^ orange brown
```

The data structure pulled from that code would be identical to the previous example above for this array.

Since each extension line is processed individually, you can combine the space-delimited and pipe-delimited formats. In this case, we can add some color names to our list that have multiple words in them.

```
! array colors = red green blue cyan magenta yellow
^ light red|light green|light blue|light cyan|light magenta|light yellow
^ dark red|dark green|dark blue|dark cyan|dark magenta|dark yellow
^ white orange teal brown pink
^ dark white|dark orange|dark teal|dark brown|dark pink
```

Finally, if your array consists of almost entirely single-word items, and you want to add in just one multi-word item, but don't want to require an extra line of RiveScript code to accomplish this, just use the `\s` tag where you need spaces to go.

```
! array blues = azure blue aqua cyan baby\sblue sky\sblue
```

#### sub
The sub variables are for defining substitutions that should be run against the client's message before any attempts are made to match it to a reply.

The interpreter should do the minimum amount of formatting possible on the client's message until after it has been passed through all the substitution patterns.

> **NOTE:** Spaces are allowed in both the variable name and the value fields.

Examples:

```
! sub what's  = what is
! sub what're = what are
! sub what'd  = what did
! sub a/s/l   = age sex location
! sub brb     = be right back
! sub afk     = away from keyboard
! sub l o l   = lol
```
