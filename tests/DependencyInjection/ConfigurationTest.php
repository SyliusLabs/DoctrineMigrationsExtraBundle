<?php

declare(strict_types=1);

namespace Tests\SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }

    /** @test */
    public function migrations_list_is_not_required(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['migrations' => []]
        );
    }

    /** @test */
    public function migrations_list_is_empty(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['migrations' => []]],
            ['migrations' => []]
        );
    }

    /** @test */
    public function migrations_list_includes_an_empty_namespace(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['migrations' => ['Name\\Space' => []]]],
            ['migrations' => ['Name\\Space' => []]]
        );
    }

    /** @test */
    public function migrations_list_includes_an_empty_namespace_as_null(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['migrations' => ['Name\\Space' => null]]],
            ['migrations' => ['Name\\Space' => []]]
        );
    }

    /** @test */
    public function migrations_list_includes_namespaces_with_required_namespaces(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['migrations' => ['Name\\Space' => ['Another\\Name\\Space']]]],
            ['migrations' => ['Name\\Space' => ['Another\\Name\\Space']]]
        );
    }
}
