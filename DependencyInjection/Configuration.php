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
        /*
        $node
            ->children()
                ->arrayNode( 'directories' )
                    ->defaultValue(
                        array(
                            'default' => array(
                                'servers' => array(
                                    'primary' => array(
                                        'host' => 'localhost',
                                        'port' => 389
                                    )
                                ),
                                'protocol_version' => 3,
                                'referrals' => 0,
                                'network_timeout' => 20,
                            ),
                        )
                    )
                    ->useAttributeAsKey( 'id' )
                    ->prototype( 'array' )
                        ->children()
                            ->arrayNode( 'servers' )
                                ->useAttributeAsKey( 'id' )
                                ->prototype( 'array' )
                                    ->children()
                                        ->scalarNode( 'host' )->cannotBeEmpty()->isRequired()->end()
                                        ->scalarNode( 'port' )->cannotBeEmpty()->defaultValue( 389 )->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode( 'protocol_version' )->defaultValue( 3 )->end()
                            ->scalarNode( 'referrals' )->defaultValue( 0 )->end()
                            ->scalarNode( 'network_timeout' )->defaultValue( 20 )->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode( 'default_directory' )->cannotBeEmpty()->defaultValue( 'default' )->cannotBeEmpty()->end()
            ->end()
        ;
        */
        return $treeBuilder;
    }
}
