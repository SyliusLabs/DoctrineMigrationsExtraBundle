<?php

declare(strict_types=1);

namespace SyliusLabs\DoctrineMigrationsExtraBundle\Comparator;

use MJS\TopSort\Implementations\ArraySort;

final class TopologicalMap
{
    /**
     * @psalm-var array<string, list<string>>
     *
     * @var array[]
     */
    private $packages;

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
        $this->packages = $packages;
        $this->dependencies = $this->buildDependencies($this->packages);
    }

    public function getPriority(string $package): int
    {
        if (!array_key_exists($package, $this->dependencies)) {
            $this->packages[$package] = [];
            $this->dependencies = $this->buildDependencies($this->packages);
        }

        return $this->dependencies[$package];
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
