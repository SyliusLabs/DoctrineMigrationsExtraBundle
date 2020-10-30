<?php

declare(strict_types=1);

namespace SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

trait DoctrineMigrationsPathsAdderTrait
{
    /**
     * Add a doctrine migration path, by taking care of user config
     * Paths is an array with migrations namespace as key, and migrations location as value:
     * @example $paths = ['MyAwesomeBundle\Migrations' => '@MyAwesomeBundle/migration'];
     *
     * @param array<string, string> $paths
     */
    private function addDoctrineMigrationPaths(array $paths, ContainerBuilder $container): void
    {
        if (! \in_array(PrependExtensionInterface::class, \class_implements(static::class), true)) {
            throw new \BadMethodCallException(sprintf('Doctrine migration path can be added only in a class that implement %s', PrependExtensionInterface::class));
        }

        if (!$container->hasExtension('doctrine_migrations')) {
            // this should not happen because it's required by this bundle, but we add to check doctrine_migrations is well registered (in bundles.php)
            throw new \DomainException('Please make sure that DoctrineMigrationBundle is registered');
        }

        $doctrineConfig = $container->getExtensionConfig('doctrine_migrations');
        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => \array_merge(\array_pop($doctrineConfig)['migrations_paths'] ?? [], $paths),
        ]);
    }
}
