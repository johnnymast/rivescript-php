<?php

/**
 * Bootstrap the Rivescript client.
 *
 * @package      Rivescript-php
 * @subpackage   Core
 * @category     Client
 * @author       Shea Lewis <shea.lewis89@gmail.com>
 */

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

/*
|--------------------------------------------------------------------------
| Bind Important Variables
|--------------------------------------------------------------------------
|
| Next, we will bind some important variables within the synapse so
| we will be able to resolve them when needed.
|
*/

$synapse->commands = Axiom\Collections\Collection::make(
    [
        'Redirect',
        'Response',
        'Topic',
        'Trigger',
        'Variable',
        'VariablePerson',
        'VariableSubstitute',
        'VariableGlobal',
        'VariableArray'
    ]
);

$synapse->triggers = Axiom\Collections\Collection::make(
    [
        'Atomic',
        'Wildcard',
        'Arrays'
    ]
);

$synapse->tags = Axiom\Collections\Collection::make(
    [
        'Set',
        'Add',
        'Sub',
        'Mult',
        'Div',
        'Star',
        'Bot',
        'Topic',
        'Env',
        'Random',
        "Id",
        'Input',
        'Reply',
        'Uppercase',
        'Lowercase',
        'Sentence',
        'InlineRedirect',
        'SpecialChars',
        'Formal',
        'Person',
        'Get',
    ]
);

$synapse->responses = Axiom\Collections\Collection::make(
    [
        'Atomic',
        'Condition',
        'Weighted',
        'ContinueResponse',
    ]
);

$synapse->conditions = Axiom\Collections\Collection::make(
    [
        'Equals',
        'NotEquals',
        'LessThan',
        'LessOrEqualTo',
        'GreaterThan',
        'GreaterThanOrEqual',
    ]
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Finally, we will bind some important interfaces within the synapse so
| we will be able to resolve them when needed as well.
|
*/

$synapse->memory = new Axiom\Rivescript\Cortex\Memory();
$synapse->brain = new Axiom\Rivescript\Cortex\Brain();

/*
|--------------------------------------------------------------------------
| Autoload Additional Files
|--------------------------------------------------------------------------
|
| Now we will autoload some files to aid in using the Rivescript
| interpreter.
|
*/

include 'helpers.php';
