<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Version\DbalMigrationFactory;
use Psr\Log\LoggerInterface;
use SyliusLabs\DoctrineMigrationsExtraBundle\Comparator\TopologicalVersionComparator;
use SyliusLabs\DoctrineMigrationsExtraBundle\Factory\ContainerAwareVersionFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

use function Symfony\Component\DependencyInjection\Loader\Configurator\inline_service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

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

    $services->set(TopologicalVersionComparator::class)
        ->args([[]]);
};
