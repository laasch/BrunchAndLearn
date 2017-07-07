<?php
/*
from http://eddmann.com/posts/implementing-and-using-memoization-in-php/
*/

$dbg = 1;

$memoize = function($func)
{
    return function() use ($func)
    {
        static $cache = [];

        $args = func_get_args();
        $key = md5(serialize($args));
if ($dbg > 4) { echo $key.': '; }
        if ( ! isset($cache[$key])) {
            $cache[$key] = call_user_func_array($func, $args);
        }

        return $cache[$key];
    };
};

function section($sect) {
  echo("\n\t\t======$sect======\n");
}

function t_start($header) {
  echo "$header\n";
  return microtime(true);
}

function t_end($start) {
  echo sprintf("%f\n", microtime(true) - $start);
}


section('FACTORIAL');
$val = 5;

function factorial($n) {
if ($dbg > 3) { echo "Processing $n\n"; }
  return ($n < 2) ? 1 : $n * factorial($n - 1);
}

$factorial = $memoize(function($n) use (&$factorial) {
if ($dbg > 3) { echo "Processing $n\n"; }
  return ($n < 2) ? 1 : $n * $factorial($n - 1);
});


$test = t_start('Non-Memo Result -- run 1');
factorial($val);
t_end($test);

$test = t_start('Non-Memo Result -- run 2');
factorial($val);
t_end($test);

$test = t_start('Non-Memo Result -- run 3');
factorial($val);
factorial($val*2);
t_end($test);

$test = t_start('Memo Result -- run 1');
$factorial($val);
t_end($test);

$test = t_start('Memo Result -- run 2');
$factorial($val);
t_end($test);

$test = t_start('Memo Result -- run 3');
$factorial($val);
$factorial($val*2);
t_end($test);

echo "\nResult: ".factorial($val).' / '.$factorial($val)."\n";

section('FIBONACCI');
$val = 10;

function fibonacci($n) {
    return ($n < 2) ? $n : fibonacci($n - 1) + fibonacci($n - 2);
}

$fibonacci = $memoize(function($n) use (&$fibonacci) {
    return ($n < 2) ? $n : $fibonacci($n - 1) + $fibonacci($n - 2);
});

$test = t_start('Non-Memo Result');
fibonacci($val);
fibonacci($val*2);
t_end($test);

$test = t_start('Memo Result');
$fibonacci($val);
$fibonacci($val*2);
t_end($test);

echo "\nResult: ".fibonacci($val).' / '.$fibonacci($val)."\n";


section('SLEEPER');

function sleepz($time) {
    sleep($time);
    return true;
}

$sleepz = $memoize('sleepz');

$test = t_start('Non-Memo Result');
sleepz(1);
sleepz(1);
t_end($test);

$test = t_start('Memo Result');
$sleepz(1);
$sleepz(1);
t_end($test);

