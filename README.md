<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">DoctrineMigrationsExtraBundle</h1>

Adding a topological sorter and container injection to DoctrineMigrationsBundle.

## Installation

1. Require this package in your project:

```bash
composer require sylius-labs/doctrine-migrations-extra-bundle
```

2. Add this bundle to `config/bundles.php`:

```php
return [
    // ...
    SyliusLabs\DoctrineMigrationsExtraBundle\SyliusLabsDoctrineMigrationsExtraBundle::class => ['all' => true],
];
```

3. Replace original Doctrine migrations services with the ones from this bundle by adding the following config in `config/packages/doctrine_migrations.yaml`:

```yaml
doctrine_migrations:
    services:
        'Doctrine\Migrations\Version\MigrationFactory': 'SyliusLabs\DoctrineMigrationsExtraBundle\Factory\ContainerAwareVersionFactory'
        'Doctrine\Migrations\Version\Comparator': 'SyliusLabs\DoctrineMigrationsExtraBundle\Comparator\TopologicalVersionComparator'
```

## Usage

### In an application

In order to define the topology of migrations, configure it in `config/packages/sylius_labs_doctrine_migrations_extra.yaml`:

```yaml
sylius_labs_doctrine_migrations_extra:
    migrations:
        'Core\Migrations': ~
        'PluginDependingOnCommonPlugin\Migrations': ['Core\Migrations', 'CommonPlugin\Migrations']
        'CommonPlugin\Migrations': ['Core\Migrations']
        'PluginDependingOnCore\Migrations': ['Core\Migrations']
``` 

The following configuration will result in the following order:

- `Core\Migrations`
- `CommonPlugin\Migrations`
- `PluginDependingOnCommonPlugin\Migrations`
- `PluginDependingOnCore\Migrations`

### In a bundle

If you want to make your bundle define its dependencies on it own, prepend the configuration in your bundle's extension:

```php
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class AcmeExtension extends Extension implements PrependExtensionInterface
{
    // ...

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('doctrine_migrations') || !$container->hasExtension('sylius_labs_doctrine_migrations_extra')) {
            return;
        }

        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => [
                'Acme\AcmeBundle\Migrations' => '@AcmeBundle/Migrations',
            ],
        ]);

        $container->prependExtensionConfig('sylius_labs_doctrine_migrations_extra', [
            'migrations' => [
                'Acme\AcmeBundle\Migrations' => ['Core\Migrations'],
            ],
        ]);
    }
}
```

## Generating new diff

Cause this bundle will dynamically change the configuration of Doctrine Migrations, you may need to specify your own namespace like:
```yaml
# config/packages/doctrine_migrations.yaml
doctrine_migrations:
  migrations_paths:
    'App\Migrations': "%kernel.project_dir%/src/Migrations"

# config/packages/sylius_labs_doctrine_migrations_extra.yaml
sylius_labs_doctrine_migrations_extra:
  migrations:
    'App\Migrations': ~
```
After that you will be able to generate again your own migration by calling:
```bash
bin/console doctrine:migrations:diff --namespace=App\\Migrations
```

## Versioning and release cycle

This package follows [semantic versioning](https://semver.org/). 
 
Next major releases are not planned yet. Minor and patch releases will be published as needed.

Bug fixes will be provided only for the most recent minor release.
Security fixes will be provided for one year since the release of subsequent minor release.

## License

This extension is completely free and released under permissive [MIT license](LICENSE).

## Authors

It is originally created by [Kamil Kokot](https://github.com/pamil). 
See the list of [all contributors](https://github.com/SyliusLabs/DoctrineMigrationsExtraBundle/graphs/contributors). 
