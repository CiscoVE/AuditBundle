<?php
/*
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
        // Set parameters
        $container->setParameter( 'wg.audit.control_user', $config['control_user'] );
        $container->setParameter( 'wg.audit.user.class', $config['user']['class'] );
        $container->setParameter( 'wg.audit.user.property', $config['user']['property'] );
        $container->setParameter( 'wg.audit.audit_reference.class', $config['audit_reference']['class'] );
        $container->setParameter( 'wg.audit.audit_reference.property', $config['audit_reference']['property'] );
    }
}
*/