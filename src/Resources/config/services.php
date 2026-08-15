<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Version\DbalMigrationFactory;
use SyliusLabs\DoctrineMigrationsExtraBundle\Comparator\TopologicalVersionComparator;
use SyliusLabs\DoctrineMigrationsExtraBundle\Factory\ContainerAwareVersionFactory;
use SyliusLabs\DoctrineMigrationsExtraBundle\Factory\ServiceLoaderMigrationFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\inline_service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(ContainerAwareVersionFactory::class)
        ->args([
            inline_service(DbalMigrationFactory::class)
                ->args([
                    inline_service(Connection::class)
                        ->factory([service('doctrine.orm.entity_manager'), 'getConnection']),
                    service('logger'),
                ]),
            service('service_container'),
        ]);

    $services->set(ServiceLoaderMigrationFactory::class)
        ->decorate('doctrine.migrations.migrations_factory')
        ->args([
            service('doctrine.migrations.migrations_factory.inner'),
            tagged_locator('doctrine_migrations.migration'),
        ]);

    $services->set(TopologicalVersionComparator::class)
        ->args([[]]);
};
