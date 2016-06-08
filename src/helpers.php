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
        echo '<pre>'."\n";
        array_map(function($x) { print_r($x); }, func_get_args());
        echo '</pre>';
        die;
    }
}
