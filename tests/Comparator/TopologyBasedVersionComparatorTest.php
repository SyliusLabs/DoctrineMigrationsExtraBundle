<?php

declare(strict_types=1);

namespace Tests\SyliusLabs\DoctrineMigrationsExtraBundle\Comparator;

use Doctrine\Migrations\Version\Comparator;
use Doctrine\Migrations\Version\Version;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use SyliusLabs\DoctrineMigrationsExtraBundle\Comparator\TopologyBasedVersionComparator;

final class TopologyBasedVersionComparatorTest extends TestCase
{
    /** @test */
    public function it_sorts_versions_by_their_dependence(): void
    {
        $comparator = new TopologyBasedVersionComparator([
            'Core' => [],
            'Addon' => ['Core'],
        ]);

        $this->assertSorting(
            $comparator,
            ['Core\\Version1', 'Core\\Version2', 'Addon\\Version1', 'Version1'],
            ['Core\\Version2', 'Version1', 'Addon\\Version1', 'Core\\Version1']
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
