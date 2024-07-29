<?php

declare(strict_types=1);

namespace SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection;

use SyliusLabs\DoctrineMigrationsExtraBundle\Comparator\TopologicalVersionComparator;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusLabsDoctrineMigrationsExtraExtension extends Extension
{
    /**
     * @param array<string, mixed> $configs
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $container->getDefinition(TopologicalVersionComparator::class)->setArgument(0, $config['migrations']);
    }

    /**
     * @param array<string, mixed> $config
     */
    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }
}
