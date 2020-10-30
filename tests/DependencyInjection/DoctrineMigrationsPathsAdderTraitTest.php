<?php

declare(strict_types=1);

namespace Tests\SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection;

use Doctrine\Bundle\MigrationsBundle\DependencyInjection\DoctrineMigrationsExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\DoctrineMigrationsPathsAdderTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class DoctrineMigrationsPathsAdderTraitTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setParameter('kernel.bundles_metadata', []);
    }

    public function testItAddPathToDoctrineConfiguration(): void
    {
        $this->load();

        $doctrineConfig = $this->container->getExtensionConfig('doctrine_migrations');

        self::assertArrayHasKey('migrations_paths', $doctrineConfig[0]);
        self::assertCount(1, $doctrineConfig[0]['migrations_paths']);
        self::assertArrayHasKey('MyAwesomeBundle\Migrations', $doctrineConfig[0]['migrations_paths']);
    }

    public function testItKeepPreviouslySettedPathInFirstPosition(): void
    {
        $this->container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => [
                'DoctrineMigrations' => '../migrations'
            ],
        ]);

        $this->load();

        $doctrineConfig = $this->container->getExtensionConfig('doctrine_migrations');
        self::assertArrayHasKey('migrations_paths', $doctrineConfig[0]);
        self::assertCount(2, $doctrineConfig[0]['migrations_paths']);

        self::assertSame('DoctrineMigrations', array_key_first($doctrineConfig[0]['migrations_paths']));
        self::assertSame('MyAwesomeBundle\Migrations', array_key_last($doctrineConfig[0]['migrations_paths']));
    }

    protected function getContainerExtensions(): array
    {
        return [
            new DoctrineMigrationsExtension(),
            new class extends Extension implements PrependExtensionInterface {

                use DoctrineMigrationsPathsAdderTrait;

                public function load(array $configs, ContainerBuilder $container): void
                {
                    // do nothing
                }

                public function getAlias(): string
                {
                    return 'testing';
                }

                public function prepend(ContainerBuilder $container): void
                {
                    $this->addDoctrineMigrationPaths(
                        ['MyAwesomeBundle\Migrations' => '../MyAwesomeBundle/migrations'],
                        $container
                    );
                }
            }
        ];
    }
}
