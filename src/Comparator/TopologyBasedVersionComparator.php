<?php

declare(strict_types=1);

namespace SyliusLabs\DoctrineMigrationsExtraBundle\Comparator;

use Doctrine\Migrations\Version\AlphabeticalComparator;
use Doctrine\Migrations\Version\Comparator;
use Doctrine\Migrations\Version\Version;
use MJS\TopSort\Implementations\ArraySort;

final class TopologyBasedVersionComparator implements Comparator
{
    /** @var Comparator */
    private $defaultSorter;

    /**
     * @psalm-var array<string, int>
     *
     * @var array
     */
    private $dependencies;

    /**
     * @psalm-param array<string, list<string>> $packages
     */
    public function __construct(array $packages)
    {
        $this->defaultSorter = new AlphabeticalComparator();
        $this->dependencies = $this->buildDependencies(array_merge($packages, ['' => []]));
    }

    public function compare(Version $a, Version $b): int
    {
        $prefixA = $this->getNamespacePrefix($a);
        $prefixB = $this->getNamespacePrefix($b);

        return $this->dependencies[$prefixA] <=> $this->dependencies[$prefixB] ?: $this->defaultSorter->compare($a, $b);
    }

    private function getNamespacePrefix(Version $version): string
    {
        $version = (string) $version;

        return substr($version, 0, strrpos($version, '\\') ?: 0);
    }

    /**
     * @psalm-param array<string, list<string>> $packages
     *
     * @psalm-return array<string, int>
     */
    private function buildDependencies(array $packages): array
    {
        $sorter = new ArraySort();

        foreach ($packages as $subject => $dependencies) {
            $sorter->add($subject, $dependencies);
        }

        /** @psalm-var array<int, string> $sorted */
        $sorted = $sorter->sort();

        return array_flip($sorted);
    }
}
