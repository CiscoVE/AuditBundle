<?php

namespace WG\AuditBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class WGAuditExtension extends Extension
{
    public function load( array $configs, ContainerBuilder $container )
    {
        // Configuration
        $configuration = new Configuration();
        $config = $this->processConfiguration( $configuration, $configs );
        // Services
        $fileLocator = new FileLocator( __DIR__ . '/../Resources/config' );
        $loader = new Loader\YamlFileLoader( $container, $fileLocator );
        $loader->load( 'services.yml' );
        /*
        // Set parameters
        $defDir = $config['default_directory'];
        if ( !isset( $config['directories'][$defDir] ) )
        {
            throw new \InvalidArgumentException( 'WGLdapBundle says "Configured default directory is not defined."' );
        }
        if ( count( $config['directories'][$defDir]['servers'] ) < 1 )
        {
            throw new \InvalidArgumentException( 'WGLdapBundle says "At least one directory server must be defined."' );
        }
        $container->setParameter( 'wg.ldap.default_directory', $config['default_directory'] );
        $container->setParameter( 'wg.ldap.directories', $config['directories'] );
        */
    }
}
