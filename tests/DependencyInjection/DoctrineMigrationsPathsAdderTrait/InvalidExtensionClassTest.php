<?php

declare(strict_types=1);

namespace Tests\SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\DoctrineMigrationsPathsAdderTrait;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\DoctrineMigrationsPathsAdderTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class InvalidExtensionClassTest extends AbstractExtensionTestCase
{

    public function testItThrowAnExceptionWhenNotImplementPrependExtension(): void
    {
        self::expectException(\BadMethodCallException::class);
        self::expectExceptionMessage('Doctrine migration path can be added only in a class that implement Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface');

        $this->load();
    }

    protected function getContainerExtensions(): array
    {
        return [new class() extends Extension {

            use DoctrineMigrationsPathsAdderTrait;

            public function load(array $configs, ContainerBuilder $container): void
            {
                $this->addDoctrineMigrationPaths([], $container);
            }

            public function getAlias(): string
            {
                return 'testing';
            }
        }];
    }
}
