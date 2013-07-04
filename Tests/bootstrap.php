<?php

if ( file_exists( $file = __DIR__ . '/autoload.php' ))
{
    require_once $file;
}
else
{
    require_once __DIR__ . '/autoload.php.dist';
}

//require_once __DIR__ . '/Functional/app/WebTestCase.php';
// Import the ClassLoader
use Doctrine\Common\ClassLoader;

// Autoloader for Doctrine
require '/../../php/PEAR/Doctrine/Common/ClassLoader.php';

// Autoloading for Doctrine
$doctrineClassLoader = new ClassLoader('Doctrine', realpath(__DIR__ . '/../../php/PEAR'));
$doctrineClassLoader->register();