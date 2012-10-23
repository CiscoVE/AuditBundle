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
                ->scalarNode( 'control_user' )->defaultNull()->end()
                ->arrayNode( 'user' )
                    ->children()
                        ->scalarNode( 'class' )->defaultNull()->end()
                        ->scalarNode( 'property' )->defaultValue( 'id' )->end()
                    ->end()
                ->end()
                ->arrayNode( 'audit_reference' )
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
