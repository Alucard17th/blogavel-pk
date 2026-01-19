<?php

declare(strict_types=1);

namespace Blogavel\Blogavel;

use Blogavel\Blogavel\Console\Commands\BlogavelDemoCommand;
use Illuminate\Support\ServiceProvider;

final class BlogavelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/blogavel.php', 'blogavel');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BlogavelDemoCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/blogavel.php' => $this->app->configPath('blogavel.php'),
        ], 'blogavel-config');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'blogavel');

        $this->publishes([
            __DIR__.'/../resources/views' => $this->app->resourcePath('views/vendor/blogavel'),
        ], 'blogavel-views');

        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/blogavel'),
        ], 'blogavel-lang');
    }
}
