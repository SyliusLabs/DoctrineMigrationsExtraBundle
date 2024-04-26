<?php

declare(strict_types=1);

namespace SyliusLabs\DoctrineMigrationsExtraBundle\Factory;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerAwareVersionFactory implements MigrationFactory
{
    /** @var MigrationFactory */
    private $migrationFactory;

    /** @var ContainerInterface */
    private $container;

    public function __construct(MigrationFactory $migrationFactory, ContainerInterface $container)
    {
        $this->migrationFactory = $migrationFactory;
        $this->container = $container;
    }

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        $instance = $this->migrationFactory->createVersion($migrationClassName);

        if (
            interface_exists(ContainerAwareInterface::class)
            && $instance instanceof ContainerAwareInterface
        ) {
            $instance->setContainer($this->container);
        }

        return $instance;
    }
}
