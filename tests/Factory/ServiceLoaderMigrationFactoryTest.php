<?php
declare(strict_types=1);

namespace Tests\SyliusLabs\DoctrineMigrationsExtraBundle\Factory;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use SyliusLabs\DoctrineMigrationsExtraBundle\Factory\ServiceLoaderMigrationFactory;

final class ServiceLoaderMigrationFactoryTest extends TestCase
{
    /** @test */
    public function migration_present_in_locator_is_returned(): void
    {
        // Arrange
        $locator = $this->createMock(ContainerInterface::class);
        $fallback = $this->createMock(MigrationFactory::class);
        $migration = $this->createMock(AbstractMigration::class);

        $locator->method('has')->with('Some\\Migration')->willReturn(true);
        $locator->method('get')->with('Some\\Migration')->willReturn($migration);
        $fallback->expects($this->never())->method('createVersion');

        $factory = new ServiceLoaderMigrationFactory($fallback, $locator);

        // Act
        $result = $factory->createVersion('Some\\Migration');

        // Assert
        Assert::assertSame($migration, $result);
    }

    /** @test */
    public function migration_missing_in_locator_is_delegated_to_fallback(): void
    {
        // Arrange
        $locator = $this->createMock(ContainerInterface::class);
        $fallback = $this->createMock(MigrationFactory::class);
        $migration = $this->createMock(AbstractMigration::class);

        $locator->method('has')->with('Some\\Migration')->willReturn(false);
        $fallback->method('createVersion')->with('Some\\Migration')->willReturn($migration);

        $factory = new ServiceLoaderMigrationFactory($fallback, $locator);

        // Act
        $result = $factory->createVersion('Some\\Migration');

        // Assert
        Assert::assertSame($migration, $result);
    }
}
