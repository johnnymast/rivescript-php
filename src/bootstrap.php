<?php

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

$synapse = new Vulcan\Rivescript\Support\Synapse;

/*
|--------------------------------------------------------------------------
| Bind Important Variables
|--------------------------------------------------------------------------
|
| Next, we will bind some important variables within the synapse so
| we will be able to resolve them when needed.
|
*/

$synapse->commands = Vulcan\Collections\Collection::make(['Trigger', 'Response', 'Variable', 'VariableSubstitute']);
$synapse->triggers = Vulcan\Collections\Collection::make(['Atomic']);
$synapse->tags     = Vulcan\Collections\Collection::make(['Bot']);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Finally, we will bind some important interfaces within the synapse so
| we will be able to resolve them when needed as well.
|
*/

$synapse->memory = new Vulcan\Rivescript\Interpreter\Memory;
$synapse->brain  = new Vulcan\Rivescript\Interpreter\Brain;
