<?php

declare(strict_types=1);

namespace Tests\SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\DoctrineMigrationsPathsAdderTrait;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\DoctrineMigrationsPathsAdderTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class DoctrineNotLoadedTest extends AbstractExtensionTestCase
{
    public function testItThrowAnExceptionWhenDoctrineIsNotRegisteredExtension(): void
    {
        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Please make sure that DoctrineMigrationBundle is registered');

        $this->load();
    }

    protected function getContainerExtensions(): array
    {
        return [new class() extends Extension implements PrependExtensionInterface {

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
                $this->addDoctrineMigrationPaths([], $container);
            }
        }];
    }
}
