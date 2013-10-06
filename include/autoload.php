<?php
    
/**
 * Autoload Classes when needed
 *
 * @param string $className Name of the class to load
 *
 * @return null
 */
function autoLoader($className)
{
    $path = '/classes/';

    include_once $path.$className.'.php';
}

spl_autoload_register('autoLoader');

?>
