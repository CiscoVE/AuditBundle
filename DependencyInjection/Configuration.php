<?php

namespace WG\AuditBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root( 'wg_audit' );
        $node
            ->children()
                ->scalarNode( 'control_user' )
                    ->defaultFalse()
                    ->validate()
                        ->ifNotInArray( array( false, true ))
                        ->thenInvalid( 'Invalid value %s for option `control_user`.' )
                    ->end()
                ->end()
                ->arrayNode( 'user' )
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode( 'class' )->defaultNull()->end()
                        ->scalarNode( 'property' )->defaultValue( 'id' )->end()
                    ->end()
                ->end()
                ->arrayNode( 'audit_reference' )
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode( 'class' )->defaultNull()->end()
                        ->scalarNode( 'property' )->defaultValue( 'id' )->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
