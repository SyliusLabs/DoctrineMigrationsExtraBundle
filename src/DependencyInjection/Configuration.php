<?php

declare(strict_types=1);

namespace SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_labs_doctrine_migrations_extra');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('migrations')
                    ->useAttributeAsKey('subject')
                    ->arrayPrototype()
                        ->performNoDeepMerging()
                        ->scalarPrototype()
        ;

        return $treeBuilder;
    }
}
