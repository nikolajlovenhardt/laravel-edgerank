<?php

namespace LaravelEdgeRank;

use Illuminate\Support\ServiceProvider;

class LaravelEdgeRankProvider extends ServiceProvider
{
    public function register()
    {
        $this->configure();
    }

    /**
     * Setup the configuration for EdgeRank.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/edgerank.php', 'edgerank'
        );
    }

    public function boot()
    {
        $this->registerPublishing();
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/edgerank.php' => $this->app->configPath('edgerank.php'),
            ], 'edgerank-config');

            $publishesMigrationsMethod = method_exists($this, 'publishesMigrations')
                ? 'publishesMigrations'
                : 'publishes';

            $this->{$publishesMigrationsMethod}([
                __DIR__.'/../../database/migrations' => $this->app->databasePath('migrations'),
            ], 'edgerank-migrations');
        }
    }
}
