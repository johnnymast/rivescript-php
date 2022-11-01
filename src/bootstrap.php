<?php
/*
 * This file is part of Rivescript-php
 *
 * (c) Shea Lewis <shea.lewis89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Commands
 */

use Axiom\Rivescript\Cortex\Commands\Definition\Arrays;
use Axiom\Rivescript\Cortex\Commands\Definition\Globals;
use Axiom\Rivescript\Cortex\Commands\Definition\Person;
use Axiom\Rivescript\Cortex\Commands\Definition\Sub;
use Axiom\Rivescript\Cortex\Commands\Definition\Variable;
use Axiom\Rivescript\Cortex\Commands\Definition\Version;
use Axiom\Rivescript\Cortex\Tags\Bot;
use Axiom\Rivescript\Cortex\Tags\Chars;
use Axiom\Rivescript\Cortex\Tags\Env;
use Axiom\Rivescript\Cortex\Tags\Formal;
use Axiom\Rivescript\Cortex\Tags\Id;
use Axiom\Rivescript\Cortex\Tags\Input;
use Axiom\Rivescript\Cortex\Tags\Reply;
use Axiom\Rivescript\Cortex\Tags\Star;
use Axiom\Rivescript\Cortex\Tags\Random;
use Axiom\Rivescript\Cortex\Tags\Person as PersonTag;

/*
|--------------------------------------------------------------------------
| Create The Synapse
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Synapse connection, which
| will act as the "glue" between the core pieces of the Rivescript
| Interpreter.
|
*/

$synapse = new Axiom\Rivescript\Cortex\Synapse();

//
/*
|--------------------------------------------------------------------------
| Bind Command types
|--------------------------------------------------------------------------
|
| These are all the commands listen in the Rivescript
| working draft stated here: https://www.rivescript.com/wd/RiveScript
*/
$synapse->commands = Axiom\Collections\Collection::make(
    items: [
        Sub::class,
        Person::class,
        Globals::class,
        // local here
        Arrays::class,
        Variable::class,
        Version::class,
        // Redirect
        // Response
        // Topic

    ]
);
//
//$synapse->triggers = Axiom\Collections\Collection::make(
//    [
//        "Atomic",
//     //   "Arrays",
//        "Alternation",
//        "Wildcard",
//        "RedirectCommand",
//        "Optional",
//    ]
//);
//
/**
 * Within Replies
 * The order that the Tags should be processed within a response or anywhere else that a tag is allowed is as follows:
 *
 * <star>      # Static text macros
 * <botstar>/<botstarN> # will match any wildcards that matched the bot's previous response.
 * <input>     #
 * <reply>     #
 * <id>        #
 * \s          #
 * \n          #
 * \\          #
 * \#          #
 * {random}    # Random text insertion (which may contain other Tags)
 * <bot>       # Insert bot variables
 * <env>       # Insert environment variables
 * <person>    # String modifiers
 * <formal>    #
 * <sentence>  #
 * <uppercase> #
 * <lowercase> #
 * <set>       # User variable modifiers
 * <add>       #
 * <sub>       #
 * <mult>      #
 * <div>       #
 * <get>       # Get user variables
 * {topic}     # Set user topic
 * <@>         # Inline redirection
 * (@array)    # Arrays
 */
$synapse->tags = Axiom\Collections\Collection::make([
    // FIXME: Remove parsing the starts from the star class or it wont ever be found for tags like <person>

    Star::class,
//    Input::class,
//    Reply::class,
//    Id::class,
//    Chars::class,
//    Random::class,

    Bot::class,
    Env::class,
    PersonTag::class,
//    Formal::class,

    //    "BotStar",
    //    "Input",
    //    "Reply",
    //    "Id",
    //    "SpecialChars",
    //    "Random",
    //    "Person",
    //    "Formal",
    //    "Set",
    //    "Get",
    //    "Sentence",
    //    "Uppercase",
    //    "Lowercase",
    //    "Bot",
    //    "Add",
    //    "Sub",
    //    "Mult",
    //    "Div",
    //    "Env",
    //    "Topic",
    //    "RedirectCommand",
    //    "ArrayTag",
    //    "OptionalTag",
    //    "Ok",
]);
//
//$synapse->responses = Axiom\Collections\Collection::make(
//    [
//        "Weighted",
//        "ContinueResponse",
//        "Atomic",
//        "ConditionCommand",
//        "RedirectCommand",
//        "PreviousCommand"
//    ]
//);
//
//$synapse->conditions = Axiom\Collections\Collection::make(
//    [
//        "Equals",
//        "NotEquals",
//        "LessThan",
//        "LessOrEqualTo",
//        "GreaterThan",
//        "GreaterThanOrEqual",
//    ]
//);
//
///*
//|--------------------------------------------------------------------------
//| Bind Important Interfaces
//|--------------------------------------------------------------------------
//|
//| Finally, we will bind some important interfaces within the synapse so
//| we will be able to resolve them when needed as well.
//|
//*/
//
$synapse->memory = new Axiom\Rivescript\Cortex\Memory();
$synapse->brain = new Axiom\Rivescript\Cortex\Brain();
//
///*
//|--------------------------------------------------------------------------
//| Autoload Additional Files
//|--------------------------------------------------------------------------
//|
//| Now we will autoload some files to aid in using the Rivescript
//| interpreter.
//|
//*/
//
include 'helpers.php';
