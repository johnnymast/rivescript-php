<?php

/**
 * Dump the passed variable(s) and end the script.
 *
 * @param  dynamic  mixed
 * @return void
 */
if (! function_exists('dd')) {
    function dd()
    {
        array_map(function($x) { print_r($x); echo"\n"; }, func_get_args());
        die;
    }
}
