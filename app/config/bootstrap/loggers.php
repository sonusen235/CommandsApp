<?php
//! Description
/**
* @date 4/5/13 5:23 PM
* @version 0.1
* @author Dmitry Bogatsky dbogatsky@gmail.com
*/
use lithium\analysis\Logger;

if (getenv('LOGGLY_USE_MOCK') || \lithium\core\Environment::is('phpunit'))
{
    $adapter = '\app\services\logs\FileLogger';
}
else
{
    $adapter = '\app\services\logs\Loggly';
}

Logger::config(array(
    'default' => array('adapter' => $adapter),
));
