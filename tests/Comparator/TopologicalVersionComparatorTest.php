<?php

declare(strict_types=1);

namespace Tests\SyliusLabs\DoctrineMigrationsExtraBundle\Comparator;

use Doctrine\Migrations\Version\Comparator;
use Doctrine\Migrations\Version\Version;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use SyliusLabs\DoctrineMigrationsExtraBundle\Comparator\TopologicalVersionComparator;

final class TopologicalVersionComparatorTest extends TestCase
{
    /** @test */
    public function it_sorts_versions_by_their_dependence(): void
    {
        $comparator = new TopologicalVersionComparator([
            'Addon' => ['Core'],
            'Core' => [],
            'Random' => [],
        ]);

        $this->assertSorting(
            $comparator,
            ['Core\\Version1', 'Core\\Version2', 'Addon\\Version1', 'Random\\Version1', 'Version1'],
            ['Core\\Version2', 'Version1', 'Random\\Version1', 'Addon\\Version1', 'Core\\Version1']
        );
    }

    /** @test */
    public function it_sorts_versions_by_their_namespace_order(): void
    {
        $comparator = new TopologicalVersionComparator([
            'Random' => [],
            'Core' => [],
        ]);

        $this->assertSorting(
            $comparator,
            ['Random\\Version1', 'Core\\Version1', 'Core\\Version2'],
            ['Core\\Version2', 'Random\\Version1', 'Core\\Version1']
        );
    }

    /** @test */
    public function it_implicitly_adds_namespaces_by_the_order_they_are_discovered(): void
    {
        // Comparator: A and B is new
        $this->assertSorting(
            new TopologicalVersionComparator([]),
            ['Core\\Version1', 'Core\\Version2', 'Addon\\Version1', 'Random\\Version1'],
            ['Core\\Version2', 'Addon\\Version1', 'Random\\Version1', 'Core\\Version1']
        );

        // Comparator: A is new, B is known
        $this->assertSorting(
            new TopologicalVersionComparator(['Registered' => []]),
            ['Registered\\Version1', 'Core\\Version1'],
            ['Core\\Version1', 'Registered\\Version1']
        );

        // Comparator: B is new, A is known
        $this->assertSorting(
            new TopologicalVersionComparator(['Registered' => []]),
            ['Registered\\Version1', 'Core\\Version1'],
            ['Registered\\Version1', 'Core\\Version1']
        );
    }

    /** @test */
    public function it_supports_migrations_without_namespace(): void
    {
        $this->assertSorting(
            new TopologicalVersionComparator([]),
            ['Abc', 'Def', 'Ghi'],
            ['Def', 'Ghi', 'Abc']
        );
    }

    /**
     * @param string[] $expectedVersions
     * @param string[] $actualVersions
     */
    private function assertSorting(Comparator $comparator, array $expectedVersions, array $actualVersions): void
    {
        Assert::assertSame(
            array_values($expectedVersions),
            array_values($this->sort($comparator, $actualVersions))
        );
    }

    /**
     * @param string[] $versions
     *
     * @return string[]
     */
    private function sort(Comparator $comparator, array $versions): array
    {
        $versions = array_map(static function (string $version): Version {
            return new Version($version);
        }, $versions);

        uasort($versions, static function (Version $a, Version $b) use ($comparator): int {
            return $comparator->compare($a, $b);
        });

        return array_map(
            static function (Version $version): string {
                return (string) $version;
            },
            $versions
        );
    }
}
