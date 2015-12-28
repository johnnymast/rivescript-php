<?php
namespace Vulcan\Rivescript\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Rivescript extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rivescript';
    }
}
