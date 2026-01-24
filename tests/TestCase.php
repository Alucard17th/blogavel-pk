<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Tests;

use Blogavel\Blogavel\BlogavelServiceProvider;
use Blogavel\Blogavel\Tests\Support\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use DatabaseMigrations;

    /**
     * Per-test config overrides (apply before package boots via refreshApplication()).
     *
     * @var array<string, mixed>
     */
    protected static array $blogavelConfigOverrides = [];

    protected function getPackageProviders($app): array
    {
        return [
            BlogavelServiceProvider::class,
            \Laravel\Sanctum\SanctumServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');

        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('auth.defaults.guard', 'web');

        $app['config']->set('blogavel.route_prefix', 'blogavel');
        $app['config']->set('blogavel.admin_prefix', 'admin');
        $app['config']->set('blogavel.api_admin_auth', 'sanctum');

        foreach (static::$blogavelConfigOverrides as $key => $value) {
            $app['config']->set("blogavel.{$key}", $value);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    protected function refreshApplicationAndMigrate(): void
    {
        $this->refreshApplication();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->artisan('migrate', ['--force' => true]);
    }
}
