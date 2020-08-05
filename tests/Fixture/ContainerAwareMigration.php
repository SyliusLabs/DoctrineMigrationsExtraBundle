<?php

declare(strict_types=1);

namespace Tests\SyliusLabs\DoctrineMigrationsExtraBundle\Fixture;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerAwareMigration extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface|null */
    private $container;

    public function up(Schema $schema): void
    {

    }

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }
}
