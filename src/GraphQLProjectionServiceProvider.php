<?php

declare(strict_types=1);

namespace GraphQLProjection;

use GraphQLProjection\Commands\GenerateGraphQLProjectionCommand;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class GraphQLProjectionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/graphql-projection.php',
            'graphql-projection'
        );
    }

    public function boot(): void
    {
        $this->registerConfigs();
        $this->registerCommands();
        $this->about();
    }

    private function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands(
            [
                GenerateGraphQLProjectionCommand::class,
            ]
        );
    }

    private function registerConfigs(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../config/graphql-projection.php' => config_path('graphql-projection.php'),
            ],
            'config-graphql-projection',
        );
    }

    private function about(): void
    {
        AboutCommand::add(
            'GraphQL Schema projection generator',
            fn () => ['Version' => GraphQLProjectionConst::VERSION]
        );
    }
}
