<?php

declare(strict_types=1);

namespace Tests\SyliusLabs\DoctrineMigrationsExtraBundle\Factory;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Version\MigrationFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SyliusLabs\DoctrineMigrationsExtraBundle\Factory\ContainerAwareVersionFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Tests\SyliusLabs\DoctrineMigrationsExtraBundle\Fixture\ContainerAwareMigration;
use Tests\SyliusLabs\DoctrineMigrationsExtraBundle\Fixture\NotContainerAwareMigration;

final class ContainerAwareVersionFactoryTest extends TestCase
{
    /** @test */
    public function migrations_implementing_container_aware_interface_are_injected_with_container(): void
    {
        if (Kernel::MAJOR_VERSION >= 7) {
            $this->markTestSkipped();
        }

        // Arrange
        $decoratedFactory = $this->createMock(MigrationFactory::class);
        $container = $this->createMock(ContainerInterface::class);

        $factory = new ContainerAwareVersionFactory($decoratedFactory, $container);

        $decoratedFactory->method('createVersion')->willReturn(new ContainerAwareMigration(
            $this->createMock(Connection::class),
            $this->createMock(LoggerInterface::class)
        ));

        // Act
        $migration = $factory->createVersion('Some\\Class');

        // Assert
        Assert::assertInstanceOf(ContainerAwareMigration::class, $migration);
        Assert::assertInstanceOf(ContainerInterface::class, $migration->getContainer());
    }

    /** @test */
    public function migrations_not_implementing_container_aware_interface_are_not_injected_with_container(): void
    {
        // Arrange
        $decoratedFactory = $this->createMock(MigrationFactory::class);
        $container = $this->createMock(ContainerInterface::class);

        $factory = new ContainerAwareVersionFactory($decoratedFactory, $container);

        $decoratedFactory->method('createVersion')->willReturn(new NotContainerAwareMigration(
            $this->createMock(Connection::class),
            $this->createMock(LoggerInterface::class)
        ));

        // Act
        $migration = $factory->createVersion('Some\\Class');

        // Assert
        Assert::assertInstanceOf(NotContainerAwareMigration::class, $migration);
        Assert::assertNull($migration->getContainer());
    }
}
