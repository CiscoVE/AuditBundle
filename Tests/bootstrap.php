<?php

spl_autoload_register( function($class)
{
    //if ( 0 === strpos( $class, 'Bundle\\Kris\\FacebookBundle\\' ))
    if ( 0 === strpos( $class, 'WG\\AuditBundle\\' ))
    {
        $path = implode( '/', array_slice( explode( '\\', $class ), 3 )) . '.php';
        require_once __DIR__ . '/../' . $path;
        return true;
    }
});

$file = __DIR__.'/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies to run test suite. "php composer.phar install --dev"');
}
require_once $file;

require_once $_SERVER['SYMFONY'] . '/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace('Symfony', $_SERVER['SYMFONY']);
$loader->register();
