<?php

namespace CiscoSystems\AuditBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class CiscoSystemsAuditExtension extends Extension
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
        $container->setParameter( 'cisco.audit.control_user', $config['control_user'] );
        $userClass = $config['user']['class'];
        if ( $userClass )
        {
            if ( false == class_exists( $userClass, true ))
            {
                throw new \InvalidArgumentException( sprintf(
                    'The option `%s` contains %s but it is not a valid class name.',
                    'user.class',
                    $userClass
                ));
            }
            $method = 'get' . ucfirst( $config['user']['property'] );
            if ( !method_exists( $userClass, $method ))
            {
                throw new \InvalidArgumentException( sprintf(
                    'The option `%s` contains %s but the class %s does not have a getter method for that property.',
                    'user.property',
                    $config['user']['property'],
                    $userClass
                ));
            }
        }
        $container->setParameter( 'cisco.audit.user.class', $userClass );
        $container->setParameter( 'cisco.audit.user.property', $config['user']['property'] );
        $auditReferenceClass = $config['audit_reference']['class'];
        if ( $auditReferenceClass )
        {
            if ( false == class_exists( $auditReferenceClass, true ))
            {
                throw new \InvalidArgumentException( sprintf(
                    'The option `%s` contains %s but it is not a valid class name.',
                    'audit_reference.class',
                    $auditReferenceClass
                ));
            }
            $method = 'get' . ucfirst( $config['audit_reference']['property'] );
            if ( !method_exists( $auditReferenceClass, $method ))
            {
                throw new \InvalidArgumentException( sprintf(
                    'The option `%s` contains %s but the class %s does not have a getter method for that property.',
                    'audit_reference.property',
                    $config['audit_reference']['property'],
                    $auditReferenceClass
                ));
            }
        }
        $container->setParameter( 'cisco.audit.audit_reference.class', $auditReferenceClass );
        $container->setParameter( 'cisco.audit.audit_reference.property', $config['audit_reference']['property'] );
        $metadataClass = $config['metadata']['class'];
        if ( $metadataClass )
        {
            if ( false == class_exists( $metadataClass, true ))
            {
                throw new \InvalidArgumentException( sprintf(
                    'The option `%s` contains %s but it is not a valid class name.',
                    'metadata.class',
                    $metadataClass
                ));
            }
            $method = 'get' . ucfirst( $config['metadata']['property'] );
            if ( !method_exists( $metadataClass, $method ))
            {
                throw new \InvalidArgumentException( sprintf(
                    'The option `%s` contains %s but the class %s does not have a getter method for that property.',
                    'metadata.property',
                    $config['metadata']['property'],
                    $metadataClass
                ));
            }
        }
        $container->setParameter( 'cisco.audit.metadata.class', $metadataClass );
        $container->setParameter( 'cisco.audit.metadata.property', $config['metadata']['property'] );
    }
}
