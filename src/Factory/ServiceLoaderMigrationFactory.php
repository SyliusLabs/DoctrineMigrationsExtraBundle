<?php
declare(strict_types=1);

namespace SyliusLabs\DoctrineMigrationsExtraBundle\Factory;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Psr\Container\ContainerInterface;

final class ServiceLoaderMigrationFactory implements MigrationFactory
{
    /**
     * @param \Symfony\Component\DependencyInjection\ServiceLocator<AbstractMigration> $locator
     */
    public function __construct(
        private MigrationFactory $fallbackFactory,
        private ContainerInterface $locator,
    ) {}

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        if ($this->locator->has($migrationClassName)) {
            return $this->locator->get($migrationClassName);
        }

        return $this->fallbackFactory->createVersion($migrationClassName);
    }
}
