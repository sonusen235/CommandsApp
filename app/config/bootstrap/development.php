<?php
/**
 * FirePHP, use in code to dump: fb($var)
 */

use lithium\aop\Filters;
use lithium\data\Connections;

if (file_exists(LITHIUM_LIBRARY_PATH.'/FirePHPCore/fb.php'))
{
    require LITHIUM_LIBRARY_PATH.'/FirePHPCore/fb.php';
}
elseif (function_exists('stream_resolve_include_path') && stream_resolve_include_path('FirePHPCore/fb.php'))
{
    require 'FirePHPCore/fb.php';
}

/*
 * Here you can store your filters/shortcuts for dump fuctional
 * It's your own file, ignored buy git
 */

// show database queries after q() in code
function q($fb = false)
{

    $connection = Connections::get('default');
    Filters::apply($connection, '_execute', function($params, $next)use($fb)
    {
        $time = microtime(true);
        $time = microtime(true) - $time;

        $echo = implode("\n", array(
            $params['sql'],
            $time,
        ));

        if ($fb && function_exists('fb')) { fb($echo); }
        else { pr($echo); }

        return $next($params);
    });
}
// var_dump
function vd($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}
// print_r
function pr($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

function fp($data, $filename = null)
{
    if (!is_string($data)) {
        $data = var_export($data,1);
    }
    $filename OR $filename = dirname(ROOT_PATH).'/'.date('H-i-s').'.html';
    file_put_contents($filename, $data);
}
