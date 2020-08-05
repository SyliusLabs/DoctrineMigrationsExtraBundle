<?php

declare(strict_types=1);

namespace Tests\SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use SyliusLabs\DoctrineMigrationsExtraBundle\Comparator\TopologicalVersionComparator;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\SyliusLabsDoctrineMigrationsExtraExtension;

final class SyliusLabsDoctrineMigrationsExtraExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function migrations_list_is_passed_to_the_topology_based_version_comparator(): void
    {
        $this->load(['migrations' => ['Name\\Space\\' => []]]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            TopologicalVersionComparator::class,
            0,
            ['Name\\Space\\' => []]
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusLabsDoctrineMigrationsExtraExtension()];
    }
}
