<?php

declare(strict_types=1);

namespace SyliusLabs\DoctrineMigrationsExtraBundle\Comparator;

use Doctrine\Migrations\Version\AlphabeticalComparator;
use Doctrine\Migrations\Version\Comparator;
use Doctrine\Migrations\Version\Version;

final class TopologicalVersionComparator implements Comparator
{
    /** @var Comparator */
    private $defaultSorter;

    /** @var TopologicalMap */
    private $map;

    /**
     * @psalm-param array<string, list<string>> $packages
     */
    public function __construct(array $packages)
    {
        $this->defaultSorter = new AlphabeticalComparator();
        $this->map = new TopologicalMap($packages);
    }

    public function compare(Version $a, Version $b): int
    {
        $prefixA = $this->getNamespacePrefix($a);
        $prefixB = $this->getNamespacePrefix($b);

        return $this->map->getPriority($prefixA) <=> $this->map->getPriority($prefixB) ?: $this->defaultSorter->compare($a, $b);
    }

    private function getNamespacePrefix(Version $version): string
    {
        $version = (string) $version;

        return substr($version, 0, strrpos($version, '\\') ?: 0);
    }
}
